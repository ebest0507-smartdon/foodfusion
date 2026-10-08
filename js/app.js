/**
 * FoodFusion interface behaviour.
 * Recipe data is printed from the PHP arrays in includes/data.php.
 * Likes, notes, and the cookie choice stay in the browser.
 */
(function () {
    "use strict";

    var FF = window.FF || { recipes: [], tutorials: [], viewer: null };
    var COOKIE_KEY = "ff_cookie_consent";
    var COMMUNITY_KEY = "ff_community_state";
    var SIGNUP_KEY = "ff_signup_prompt";
    var currentRecipe = null;

    document.addEventListener("DOMContentLoaded", function () {
        initCookies();
        initRecipeFilters();
        initRecipeModal();
        initCommunity();
        initContactForm();
        initSignupForm();
        initSignupPrompt();
        initRegisterForm();
        initForgotForm();
        initResetForm();
        initLockout();
        initTutorials();
        initEnergyBars();
    });

    function initCookies() {
        var bar = document.getElementById("cookieBar");
        if (!bar) {
            return;
        }
        var stored = null;
        try {
            stored = localStorage.getItem(COOKIE_KEY);
        } catch (error) {
            stored = null;
        }
        if (!stored) {
            bar.hidden = false;
            document.body.classList.add("has-cookie-bar");
        }
        var accept = document.getElementById("cookieAccept");
        var reject = document.getElementById("cookieReject");
        if (accept) {
            accept.addEventListener("click", function () {
                saveCookieChoice("accepted");
            });
        }
        if (reject) {
            reject.addEventListener("click", function () {
                saveCookieChoice("rejected");
            });
        }
    }

    function saveCookieChoice(choice) {
        try {
            localStorage.setItem(COOKIE_KEY, choice);
        } catch (error) {
            /* Private browsing can block storage. The bar still closes. */
        }
        if (choice === "accepted") {
            document.cookie = "ff_prefs=accepted; path=/; max-age=" + (60 * 60 * 24 * 180) + "; SameSite=Lax";
        } else {
            document.cookie = "ff_prefs=; path=/; max-age=0; SameSite=Lax";
        }
        var bar = document.getElementById("cookieBar");
        if (bar) {
            bar.hidden = true;
        }
        document.body.classList.remove("has-cookie-bar");
    }

    function initRecipeFilters() {
        var form = document.getElementById("recipeFilters");
        if (!form) {
            return;
        }
        form.addEventListener("submit", function (event) {
            event.preventDefault();
        });
        form.addEventListener("change", applyRecipeFilters);
        var reset = document.getElementById("filterReset");
        if (reset) {
            reset.addEventListener("click", function () {
                form.reset();
                applyRecipeFilters();
            });
        }
        applyRecipeFilters();
    }

    function applyRecipeFilters() {
        var cuisine = valueOf("filterCuisine");
        var diet = valueOf("filterDiet");
        var difficulty = valueOf("filterDifficulty");
        var cards = document.querySelectorAll("[data-recipe-card]");
        var visible = 0;
        cards.forEach(function (card) {
            var match = (cuisine === "" || card.dataset.cuisine === cuisine)
                && (diet === "" || card.dataset.diet === diet)
                && (difficulty === "" || card.dataset.difficulty === difficulty);
            card.hidden = !match;
            if (match) {
                visible += 1;
            }
        });
        var count = document.getElementById("recipeCount");
        if (count) {
            count.textContent = String(visible);
        }
        var empty = document.getElementById("recipeEmpty");
        if (empty) {
            empty.hidden = visible !== 0;
        }
    }

    function initRecipeModal() {
        document.addEventListener("click", function (event) {
            var button = event.target.closest("[data-recipe-open]");
            if (!button) {
                return;
            }
            var recipe = findById(FF.recipes, button.getAttribute("data-recipe-open"));
            if (!recipe) {
                return;
            }
            fillRecipeModal(recipe);
            var modalNode = document.getElementById("recipeModal");
            if (modalNode && window.bootstrap) {
                window.bootstrap.Modal.getOrCreateInstance(modalNode).show();
            }
        });
        var download = document.getElementById("recipeDownload");
        if (download) {
            download.addEventListener("click", function () {
                if (currentRecipe) {
                    downloadRecipeCard(currentRecipe);
                }
            });
        }
    }

    function fillRecipeModal(recipe) {
        currentRecipe = recipe;
        var title = document.getElementById("recipeModalTitle");
        var body = document.getElementById("recipeModalBody");
        if (!title || !body) {
            return;
        }
        title.textContent = recipe.title;
        body.replaceChildren();

        var image = document.createElement("img");
        image.className = "recipe-modal-img";
        image.src = recipe.image_url;
        image.alt = recipe.title;
        body.appendChild(image);

        var meta = document.createElement("p");
        meta.className = "recipe-meta";
        meta.textContent = recipe.cuisine_type + " · " + recipe.dietary_pref + " · " + recipe.difficulty
            + " · " + recipe.prep_minutes + " min · " + recipe.servings + " servings · " + recipe.author;
        body.appendChild(meta);

        var description = document.createElement("p");
        description.textContent = recipe.description;
        body.appendChild(description);

        body.appendChild(heading("Ingredients"));
        body.appendChild(listFrom(recipe.ingredients));
        body.appendChild(heading("Method"));
        var steps = document.createElement("ol");
        (recipe.instructions || []).forEach(function (step) {
            var item = document.createElement("li");
            item.textContent = step;
            steps.appendChild(item);
        });
        body.appendChild(steps);
    }

    function downloadRecipeCard(recipe) {
        var lines = [
            recipe.title,
            recipe.cuisine_type + " | " + recipe.dietary_pref + " | " + recipe.difficulty,
            recipe.prep_minutes + " min, serves " + recipe.servings,
            "By " + recipe.author,
            "",
            recipe.description,
            "",
            "Ingredients",
            (recipe.ingredients || []).map(function (item) { return "- " + item; }).join("\n"),
            "",
            "Method",
            (recipe.instructions || []).map(function (step, index) { return (index + 1) + ". " + step; }).join("\n"),
            "",
            "FoodFusion recipe card"
        ];
        var blob = new Blob([lines.join("\n")], { type: "text/plain;charset=utf-8" });
        var link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = slugify(recipe.title) + ".txt";
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(link.href);
    }

    function initCommunity() {
        var form = document.getElementById("communityForm");
        var feed = document.getElementById("communityFeed");
        if (!feed) {
            return;
        }
        var state = loadCommunityState();
        state.posts.forEach(function (post) {
            feed.prepend(renderPost(post));
        });
        applyStoredLikes(state);
        applyStoredComments(state);

        if (form) {
            form.addEventListener("submit", function (event) {
                var title = valueOf("postTitle").trim();
                var tip = valueOf("postType");
                var content = valueOf("postContent").trim();
                var error = document.getElementById("communityError");
                if (title.length < 4 || content.length < 20 || tip === "") {
                    event.preventDefault();
                    if (error) {
                        error.hidden = false;
                        error.textContent = "Add a title of at least 4 characters, choose a tip type, and write at least 20 characters.";
                    }
                    return;
                }
                event.preventDefault();
                if (error) {
                    error.hidden = true;
                }
                var post = {
                    id: "local-" + Date.now(),
                    author: FF.viewer || "Guest cook",
                    title: title,
                    tip_type: tip,
                    content: content,
                    likes: 0,
                    created_at: new Date().toISOString().slice(0, 10),
                    comments: []
                };
                state.posts.unshift(post);
                state.posts = state.posts.slice(0, 20);
                saveCommunityState(state);
                feed.prepend(renderPost(post));
                form.reset();
                setStatus("communityStatus", "Added to the feed on this browser.");
            });
        }

        feed.addEventListener("click", function (event) {
            var like = event.target.closest("[data-like]");
            if (like) {
                toggleLike(like, state);
                return;
            }
            var toggle = event.target.closest("[data-comment-toggle]");
            if (toggle) {
                var panel = toggle.closest(".post-card").querySelector(".comment-panel");
                var open = panel.hidden;
                panel.hidden = !open;
                toggle.setAttribute("aria-expanded", open ? "true" : "false");
            }
        });

        feed.addEventListener("submit", function (event) {
            var commentForm = event.target.closest(".comment-form");
            if (!commentForm) {
                return;
            }
            event.preventDefault();
            var input = commentForm.querySelector("input");
            var text = input.value.trim();
            if (text === "") {
                return;
            }
            var card = commentForm.closest(".post-card");
            var id = card.dataset.postId;
            var item = document.createElement("li");
            var author = document.createElement("strong");
            author.textContent = FF.viewer || "You";
            item.appendChild(author);
            item.appendChild(document.createTextNode(" — " + text));
            card.querySelector(".comment-list").appendChild(item);
            if (!state.comments[id]) {
                state.comments[id] = [];
            }
            state.comments[id].push({ author: FF.viewer || "You", text: text });
            saveCommunityState(state);
            var count = card.querySelector("[data-comment-count]");
            count.textContent = String(card.querySelectorAll(".comment-list li").length);
            input.value = "";
        });
    }

    function renderPost(post) {
        var article = document.createElement("article");
        article.className = "post-card";
        article.dataset.postId = String(post.id);

        var head = document.createElement("div");
        head.className = "post-card-head";
        var titles = document.createElement("div");
        var eyebrow = document.createElement("p");
        eyebrow.className = "eyebrow";
        eyebrow.textContent = post.tip_type;
        var headingNode = document.createElement("h2");
        headingNode.textContent = post.title;
        titles.appendChild(eyebrow);
        titles.appendChild(headingNode);
        var meta = document.createElement("p");
        meta.className = "post-meta";
        meta.textContent = post.author + " · " + post.created_at;
        head.appendChild(titles);
        head.appendChild(meta);

        var content = document.createElement("p");
        content.textContent = post.content;

        var actions = document.createElement("div");
        actions.className = "post-actions";
        actions.appendChild(likeButton(post.likes || 0, false));
        var notes = document.createElement("button");
        notes.type = "button";
        notes.className = "btn btn-ff-ghost btn-sm";
        notes.setAttribute("data-comment-toggle", "");
        notes.setAttribute("aria-expanded", "false");
        notes.appendChild(document.createTextNode("Notes "));
        var noteCount = document.createElement("span");
        noteCount.dataset.commentCount = "";
        noteCount.textContent = String((post.comments || []).length);
        notes.appendChild(noteCount);
        actions.appendChild(notes);

        var panel = document.createElement("div");
        panel.className = "comment-panel";
        panel.hidden = true;
        var list = document.createElement("ul");
        list.className = "comment-list";
        (post.comments || []).forEach(function (comment) {
            list.appendChild(commentItem(comment));
        });
        var commentForm = document.createElement("form");
        commentForm.className = "comment-form";
        var input = document.createElement("input");
        input.className = "form-control";
        input.maxLength = 280;
        input.required = true;
        input.placeholder = "Add a short note";
        input.setAttribute("aria-label", "Add a short note");
        var submit = document.createElement("button");
        submit.className = "btn btn-ff btn-sm";
        submit.type = "submit";
        submit.textContent = "Post";
        commentForm.appendChild(input);
        commentForm.appendChild(submit);
        panel.appendChild(list);
        panel.appendChild(commentForm);

        article.appendChild(head);
        article.appendChild(content);
        article.appendChild(actions);
        article.appendChild(panel);
        return article;
    }

    function likeButton(count, pressed) {
        var button = document.createElement("button");
        button.type = "button";
        button.className = "btn btn-ff-ghost btn-sm" + (pressed ? " is-liked" : "");
        button.setAttribute("data-like", "");
        button.setAttribute("aria-pressed", pressed ? "true" : "false");
        var icon = document.createElement("i");
        icon.className = pressed ? "bi bi-heart-fill" : "bi bi-heart";
        icon.setAttribute("aria-hidden", "true");
        var number = document.createElement("span");
        number.dataset.likeCount = "";
        number.textContent = String(count);
        button.appendChild(icon);
        button.appendChild(document.createTextNode(" "));
        button.appendChild(number);
        return button;
    }

    function toggleLike(button, state) {
        var card = button.closest(".post-card");
        var id = card.dataset.postId;
        var pressed = button.getAttribute("aria-pressed") === "true";
        var countNode = button.querySelector("[data-like-count]");
        var count = parseInt(countNode.textContent, 10) || 0;
        count += pressed ? -1 : 1;
        button.setAttribute("aria-pressed", pressed ? "false" : "true");
        button.classList.toggle("is-liked", !pressed);
        var icon = button.querySelector("i");
        icon.className = pressed ? "bi bi-heart" : "bi bi-heart-fill";
        countNode.textContent = String(count);
        state.likes[id] = { count: count, pressed: !pressed };
        saveCommunityState(state);
    }

    function applyStoredLikes(state) {
        Object.keys(state.likes).forEach(function (id) {
            var card = document.querySelector('.post-card[data-post-id="' + cssEscape(id) + '"]');
            if (!card) {
                return;
            }
            var button = card.querySelector("[data-like]");
            var saved = state.likes[id];
            button.setAttribute("aria-pressed", saved.pressed ? "true" : "false");
            button.classList.toggle("is-liked", !!saved.pressed);
            button.querySelector("i").className = saved.pressed ? "bi bi-heart-fill" : "bi bi-heart";
            button.querySelector("[data-like-count]").textContent = String(saved.count);
        });
    }

    function applyStoredComments(state) {
        Object.keys(state.comments).forEach(function (id) {
            var card = document.querySelector('.post-card[data-post-id="' + cssEscape(id) + '"]');
            if (!card) {
                return;
            }
            var list = card.querySelector(".comment-list");
            state.comments[id].forEach(function (comment) {
                list.appendChild(commentItem(comment));
            });
            card.querySelector("[data-comment-count]").textContent = String(list.children.length);
        });
    }

    function commentItem(comment) {
        var item = document.createElement("li");
        var author = document.createElement("strong");
        author.textContent = comment.author;
        item.appendChild(author);
        item.appendChild(document.createTextNode(" — " + comment.text));
        return item;
    }

    function loadCommunityState() {
        try {
            var raw = sessionStorage.getItem(COMMUNITY_KEY);
            if (!raw) {
                return { posts: [], likes: {}, comments: {} };
            }
            var parsed = JSON.parse(raw);
            return {
                posts: Array.isArray(parsed.posts) ? parsed.posts : [],
                likes: parsed.likes && typeof parsed.likes === "object" ? parsed.likes : {},
                comments: parsed.comments && typeof parsed.comments === "object" ? parsed.comments : {}
            };
        } catch (error) {
            return { posts: [], likes: {}, comments: {} };
        }
    }

    function saveCommunityState(state) {
        try {
            sessionStorage.setItem(COMMUNITY_KEY, JSON.stringify(state));
        } catch (error) {
            /* Ignore quota or private-mode failures. The feed still updates. */
        }
    }

    function initContactForm() {
        var form = document.getElementById("contactForm");
        if (!form) {
            return;
        }
        form.addEventListener("submit", function (event) {
            var fields = [
                ["contactName", "Please enter your name."],
                ["contactEmail", "Enter a valid email address."],
                ["contactSubject", "Add a subject of at least 3 characters."],
                ["contactMessage", "Write a message of at least 10 characters."]
            ];
            var valid = true;
            fields.forEach(function (pair) {
                var input = document.getElementById(pair[0]);
                var ok = fieldIsValid(input);
                input.classList.toggle("is-invalid", !ok);
                input.classList.toggle("is-valid", ok);
                if (!ok) {
                    valid = false;
                }
            });
            if (!valid) {
                event.preventDefault();
                var firstInvalid = form.querySelector(".is-invalid");
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }
        });
    }

    function fieldIsValid(input) {
        var value = input.value.trim();
        if (input.id === "contactName") {
            return value.length >= 2;
        }
        if (input.id === "contactEmail") {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        }
        if (input.id === "contactSubject") {
            return value.length >= 3;
        }
        if (input.id === "contactMessage") {
            return value.length >= 10;
        }
        return true;
    }

    function initSignupForm() {
        var form = document.getElementById("signupForm");
        if (!form) {
            return;
        }
        form.addEventListener("submit", function (event) {
            var errors = accountErrors(
                valueOf("signupFirst"),
                valueOf("signupLast"),
                valueOf("signupEmail"),
                valueOf("signupPassword")
            );
            var box = document.getElementById("signupErrors");
            if (errors.length) {
                event.preventDefault();
                if (box) {
                    box.hidden = false;
                    box.textContent = errors.join(" ");
                }
            } else if (box) {
                box.hidden = true;
                markSignupJoined();
            }
        });
    }

    function initSignupPrompt() {
        var modalEl = document.getElementById("signupModal");
        if (!modalEl) {
            return;
        }
        if (typeof bootstrap === "undefined" || !bootstrap.Modal) {
            return;
        }
        if (FF.viewer) {
            return;
        }
        if (document.body.classList.contains("page-login") ||
            document.body.classList.contains("page-register")) {
            return;
        }

        /* Older builds stored a permanent block; clear it so refresh works again. */
        try {
            localStorage.removeItem(SIGNUP_KEY);
        } catch (error) {
            /* ignore */
        }

        /* Hard refresh should bring the CTA back for guests who only dismissed it. */
        clearDismissOnReload();

        if (hasJoinedKitchen() || signupDismissedThisSession()) {
            return;
        }

        modalEl.addEventListener("hidden.bs.modal", function () {
            dismissSignupThisSession();
        });

        window.setTimeout(function () {
            if (FF.viewer || hasJoinedKitchen() || signupDismissedThisSession()) {
                return;
            }
            if (document.querySelector(".modal.show")) {
                window.setTimeout(function () {
                    openSignupPrompt(modalEl);
                }, 1200);
                return;
            }
            openSignupPrompt(modalEl);
        }, 700);
    }

    function clearDismissOnReload() {
        var nav = null;
        try {
            nav = performance.getEntriesByType("navigation")[0];
        } catch (error) {
            nav = null;
        }
        var isReload = (nav && nav.type === "reload") ||
            (typeof performance.navigation !== "undefined" && performance.navigation.type === 1);
        if (!isReload) {
            return;
        }
        try {
            if (sessionStorage.getItem(SIGNUP_KEY) === "dismissed") {
                sessionStorage.removeItem(SIGNUP_KEY);
            }
        } catch (error) {
            /* ignore */
        }
    }

    function openSignupPrompt(modalEl) {
        if (!modalEl || document.querySelector(".modal.show")) {
            return;
        }
        try {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        } catch (error) {
            /* Bootstrap may still be settling after a hard refresh. */
        }
    }

    function hasJoinedKitchen() {
        try {
            return sessionStorage.getItem(SIGNUP_KEY) === "joined";
        } catch (error) {
            return false;
        }
    }

    function signupDismissedThisSession() {
        try {
            return sessionStorage.getItem(SIGNUP_KEY) === "dismissed";
        } catch (error) {
            return false;
        }
    }

    function dismissSignupThisSession() {
        try {
            if (sessionStorage.getItem(SIGNUP_KEY) !== "joined") {
                sessionStorage.setItem(SIGNUP_KEY, "dismissed");
            }
        } catch (error) {
            /* Private browsing can block storage. */
        }
    }

    function markSignupJoined() {
        try {
            sessionStorage.setItem(SIGNUP_KEY, "joined");
            localStorage.removeItem(SIGNUP_KEY);
        } catch (error) {
            /* Private browsing can block storage. */
        }
    }

    function initRegisterForm() {
        var form = document.getElementById("registerForm");
        if (!form) {
            return;
        }
        form.addEventListener("submit", function (event) {
            var errors = accountErrors(
                valueOf("firstName"),
                valueOf("lastName"),
                valueOf("email"),
                valueOf("password")
            );
            var box = document.getElementById("registerErrors");
            if (errors.length) {
                event.preventDefault();
                if (box) {
                    box.hidden = false;
                    box.textContent = errors.join(" ");
                }
            }
        });
    }

    function initForgotForm() {
        var form = document.getElementById("forgotForm");
        if (!form) {
            return;
        }
        form.addEventListener("submit", function (event) {
            var email = valueOf("email").trim();
            var box = document.getElementById("forgotErrors");
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                event.preventDefault();
                if (box) {
                    box.hidden = false;
                    box.textContent = "Enter the email address on the account.";
                }
            } else if (box) {
                box.hidden = true;
            }
        });
    }

    function initResetForm() {
        var form = document.getElementById("resetForm");
        if (!form) {
            return;
        }
        form.addEventListener("submit", function (event) {
            var password = valueOf("password");
            var confirmPassword = valueOf("passwordConfirm");
            var errors = [];
            if (password.length < 8 || password.length > 72 || !/[A-Za-z]/.test(password) || !/\d/.test(password)) {
                errors.push("Use at least 8 characters with a letter and a number.");
            }
            if (password !== confirmPassword) {
                errors.push("Type the same password in both fields.");
            }
            var box = document.getElementById("resetErrors");
            if (errors.length) {
                event.preventDefault();
                if (box) {
                    box.hidden = false;
                    box.textContent = errors.join(" ");
                }
            } else if (box) {
                box.hidden = true;
            }
        });
    }

    function accountErrors(first, last, email, password) {
        var errors = [];
        if (first.trim().length < 2 || last.trim().length < 2) {
            errors.push("Enter your first and last name.");
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.trim())) {
            errors.push("Enter a valid email address.");
        }
        if (password.length < 8 || !/[A-Za-z]/.test(password) || !/\d/.test(password)) {
            errors.push("Use at least 8 characters with a letter and a number.");
        }
        return errors;
    }

    function initLockout() {
        var notice = document.getElementById("lockoutNotice");
        if (!notice) {
            return;
        }
        var until = parseInt(notice.getAttribute("data-until"), 10);
        var clock = document.getElementById("lockoutClock");
        var form = document.getElementById("loginForm");
        function tick() {
            var remaining = Math.max(0, until - Math.floor(Date.now() / 1000));
            if (clock) {
                var minutes = Math.floor(remaining / 60);
                var seconds = remaining % 60;
                clock.textContent = minutes + ":" + String(seconds).padStart(2, "0");
            }
            if (remaining <= 0) {
                notice.hidden = true;
                if (form) {
                    form.querySelectorAll("input, button").forEach(function (control) {
                        if (control.name !== "csrf") {
                            control.disabled = false;
                        }
                    });
                }
                setStatus("lockoutLive", "You can try signing in again.");
                return;
            }
            window.setTimeout(tick, 1000);
        }
        tick();
    }

    function initTutorials() {
        document.addEventListener("click", function (event) {
            var button = event.target.closest("[data-tutorial-open]");
            if (!button) {
                return;
            }
            var tutorial = findById(FF.tutorials, button.getAttribute("data-tutorial-open"));
            if (!tutorial) {
                return;
            }
            document.getElementById("tutorialModalTitle").textContent = tutorial.title;
            document.getElementById("tutorialLevel").textContent = tutorial.level + " · " + tutorial.minutes + " min";
            document.getElementById("tutorialSummary").textContent = tutorial.summary;
            var steps = document.getElementById("tutorialSteps");
            steps.replaceChildren();
            (tutorial.steps || []).forEach(function (step) {
                var item = document.createElement("li");
                item.textContent = step;
                steps.appendChild(item);
            });
            var modalNode = document.getElementById("tutorialModal");
            if (modalNode && window.bootstrap) {
                window.bootstrap.Modal.getOrCreateInstance(modalNode).show();
            }
        });
    }

    function initEnergyBars() {
        window.setTimeout(function () {
            document.querySelectorAll(".energy-fill").forEach(function (bar) {
                bar.classList.add("is-in");
            });
        }, 60);
    }

    function heading(text) {
        var node = document.createElement("h3");
        node.textContent = text;
        return node;
    }

    function listFrom(items) {
        var list = document.createElement("ul");
        (items || []).forEach(function (item) {
            var li = document.createElement("li");
            li.textContent = item;
            list.appendChild(li);
        });
        return list;
    }

    function findById(collection, id) {
        var wanted = String(id);
        for (var i = 0; i < collection.length; i += 1) {
            if (String(collection[i].id) === wanted) {
                return collection[i];
            }
        }
        return null;
    }

    function valueOf(id) {
        var node = document.getElementById(id);
        return node ? node.value : "";
    }

    function setStatus(id, message) {
        var node = document.getElementById(id);
        if (node) {
            node.textContent = message;
        }
    }

    function slugify(value) {
        return String(value).toLowerCase().replace(/[^a-z0-9]+/g, "-").replace(/(^-|-$)/g, "");
    }

    function cssEscape(value) {
        if (window.CSS && CSS.escape) {
            return CSS.escape(value);
        }
        return String(value).replace(/"/g, "");
    }
})();
