<?php
declare(strict_types=1);
?>
<div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content signup-modal">
            <div class="signup-modal-grid">
                <div class="signup-modal-visual" aria-hidden="true">
                    <img src="images/hero-chef-jollof.jpg" alt="">
                    <div class="signup-modal-visual-copy">
                        <p class="eyebrow">FoodFusion</p>
                        <p>Cook across cultures. Share the method.</p>
                    </div>
                </div>
                <div class="signup-modal-panel">
                    <div class="modal-header border-0 pb-0">
                        <div>
                            <p class="eyebrow mb-1">New here?</p>
                            <h2 class="modal-title fs-3" id="signupTitle">Sign up now</h2>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="post" action="register.php" id="signupForm" novalidate>
                        <div class="modal-body pt-2">
                            <p>Join the kitchen for tested recipes, community notes, and clear methods you can cook on a weeknight.</p>
                            <div id="signupErrors" class="alert alert-danger" role="alert" hidden></div>
                            <input type="hidden" name="csrf" value="<?= e(ff_csrf_token()) ?>">
                            <input type="hidden" name="source" value="modal">
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label" for="signupFirst">First name</label>
                                    <input class="form-control" id="signupFirst" name="first_name" maxlength="80" required autocomplete="given-name">
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label" for="signupLast">Last name</label>
                                    <input class="form-control" id="signupLast" name="last_name" maxlength="80" required autocomplete="family-name">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="signupEmail">Email</label>
                                    <input class="form-control" id="signupEmail" name="email" type="email" maxlength="190" required autocomplete="email">
                                </div>
                                <div class="col-12">
                                    <label class="form-label" for="signupPassword">Password</label>
                                    <input class="form-control" id="signupPassword" name="password" type="password" minlength="8" maxlength="72" required autocomplete="new-password">
                                    <p class="form-help">At least 8 characters, with a letter and a number.</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <a href="login.php">Already have an account?</a>
                            <button class="btn btn-ff" type="submit">Create account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-3" id="privacyTitle">Privacy and cookies</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>FoodFusion keeps the name, email, and password you give us when you join, along with recipes, community notes, and messages you send.</p>
                <p>A sign-in cookie remembers who you are while you are here. It is needed for the kitchen to stay open as you move between pages, and it is limited to this site.</p>
                <p>Your choice to accept or decline cookies is remembered, so we do not ask on every visit. Likes and notes you add in the cookbook stay on this device until you close the tab.</p>
                <p>We do not sell this information. Questions go to hello@foodfusion.test or 01632 960 482.</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="recipeModal" tabindex="-1" aria-labelledby="recipeModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-3" id="recipeModalTitle">Recipe</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="recipeModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ff-ghost" id="recipeDownload">Download card</button>
                <button type="button" class="btn btn-ff" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="tutorialModal" tabindex="-1" aria-labelledby="tutorialModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <p class="eyebrow mb-1" id="tutorialLevel">Tutorial</p>
                    <h2 class="modal-title fs-3" id="tutorialModalTitle">Tutorial</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="tutorialSummary"></p>
                <ol id="tutorialSteps" class="tutorial-steps"></ol>
            </div>
        </div>
    </div>
</div>

<div id="cookieBar" class="cookie-bar" hidden>
    <p>We use a cookie to keep you signed in, and we remember whether you accept any others. <button type="button" class="cookie-policy" data-bs-toggle="modal" data-bs-target="#privacyModal">Read the privacy note</button></p>
    <div class="cookie-actions">
        <button type="button" class="btn btn-ff-ghost btn-sm cookie-reject" id="cookieReject">Reject</button>
        <button type="button" class="btn btn-ff btn-sm" id="cookieAccept">Accept</button>
    </div>
</div>
