<!DOCTYPE html>
<html>
<head>
    <title>CHI-ASE Forum</title>
    <link rel="stylesheet" href="css/utilities/reset.css" />
    <link rel="stylesheet" href="css/utilities/fonts.css" />
    <link rel="stylesheet" href="css/utilities/util-text.css" />
    <link rel="stylesheet" href="css/utilities/util-padding.css" />
    <link rel="stylesheet" href="css/utilities/utility.css" />
    <link rel="stylesheet" href="css/index.css" />
    <link rel="stylesheet" href="css/form.css" />
    <link rel="stylesheet" href="css/colors.css" />
    <link rel="stylesheet" href="css/admin.css" />
    <link rel="stylesheet" href="css/utilities/responsive.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: var(--text-light);
            padding: 20px;
            border-radius: 8px;
           
            position: relative;
            display: flex;
            flex-direction: column;
            width: 400px;
            z-index: 50;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
        }
        #clearBtn {
            padding: 0.6rem;
            border: 2px solid var(--border-main-dark);
            box-shadow: inset 0 0 3px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
            background: rgba(0, 0, 0, 0.024);
            font-size: 0.875rem;
            border-radius: 0.5rem;
            font-family: "Inter", serif;
            font-optical-sizing: auto;
            font-weight: 400;
            width: calc(100% - 20px); 
            max-width: 100%;
            color: var(--text-light);
            cursor: pointer;
        }

        #clearBtn:hover {
            background-color: var(--chiase-color-dark);
            color: white;
        }
        
        form {
            width: calc(100% - 1rem);
        }
        h2 {
            padding-top: 1rem;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="grid-container-full">
        <div class="grid-item-wrapper">
            <div class="grid-item">
            <form id="signup-form" method="GET" action="validation/response-signup.php">
                <div class="form-wrapper py-4 pr-4 pl-1p5">
                    <div class="row pb-2">
                    <img class="logo" src="assets/logo.png" />
                    </div>
                    <div class="col pb-4">
                        <span style="display: flex; flex-direction: column;">
                        <h1 class="text-3xl inter-700 pb-2">Sign Up</h1>
                        <p class="text-sm inter-400">It seems like you don't have an account, or you entered the wrong credentials.</p>
                        </span>
                    </div>
                    <div class="col">
                        <input type="text" name="username" id="username" placeholder="Username" class="index" style="width: 400px;"/>
                    </div>
                    <div class="col">
                        <input type="text" name="full_name" id="full_name" placeholder="Full Name" class="index" style="width: 400px;"/>
                    </div>
                    <div class="col">
                        <input type="text" name="email" id="email" placeholder="Email" class="index" style="width: 400px;"/>
                        <div class="text-xs" style="color: red" id="email-alert"></div>
                    </div>
                    <div class="col">
                        <input type="text" name="age" id="age" placeholder="Age" class="index" style="width: 400px;"/>
                        <div class="text-xs" style="color: red" id="age-alert"></div>
                    </div>
                    <div class="col">
                        <input type="password" name="password" id="password" placeholder="Password" class="index" style="width: 400px;"/>
                    </div>
                    <div class="col">
                        <input type="password" name="repeat_password" id="repeat_password" placeholder="Repeat Password" class="index" style="width: 400px;"/>
                        <div class="text-xs" style="color: blue" id="repeat-password-alert"></div>
                    </div>
                    <div class="row">
                        <input type="checkbox" id="terms-check" name="terms-check" value="terms-check" style="width: fit-content;">
                        <p class="text-sm inter-300"> I agree to <a style="color: black; text-decoration: underline; cursor: pointer;" id="terms">terms and conditions</a></p><br>
                    </div>
                    <div class="col">
                        <input type="Submit" class="poppins-regular index" id="submit" value="Sign Up" style="width: 400px;">
                        <?php
                        if (isset($_GET['errors'])) {
                            $errors = $_GET['errors'];
                            if (is_array($errors)) {
                                echo "<div class='col'>";
                                foreach ($errors as $error) {
                                    echo "<p class='text-sm inter-400 gradient-text'>" . htmlspecialchars($error) . "</p>"; 
                                }
                                
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                    <div class="col">
                        <a href="index.php" class="text-sm inter-300">Back to login page</a>
                    </div>
                </div>
            </form>
            <div class="col">
                <button class="poppins-regular index" style="width: 200px;" id="clearBtn" onclick="openClearModal()" style="cursor: pointer;" data-theme="light">Clear</button>
            </div>
            <div id="clear-modal" class="modal">
                <div class="modal-content">
                    <h1 class="gradient-text text-2xl inter-700">Are you sure you want to clear input?</h1>
                    <div class="flex flex-row pt-4 justify-center gap-4">
                        <button type="button" class="action-button" onclick="clearInputs()">Yes</button>
                        <button type="button" onclick="closeClearModal()" class="action-button">No</button>
                    </div>
                </div>
            </div>

            <div id="terms-modal" class="modal ">
                <div class="modal-content text-black inter-400" style="width: 90%; overflow: scroll;">
                <h1 class="text-xl gradient-text inter-700">Terms and Conditions</h1>
        <p><strong>Last Updated:</strong> March 19, 2025</p>
        <h2>1. Definitions</h2>
        <ul>
            <li><strong>"Chiase"</strong> refers to our website, services, and content.</li>
            <li><strong>"User"</strong> refers to anyone who accesses or uses Chiase.</li>
            <li><strong>"Content"</strong> includes text, images, videos, posts, and any other material uploaded to the website.</li>
        </ul>

        <h2>2. Use of the Website</h2>
        <ul>
            <li>You must be at least <strong>13 years old</strong> to use Chiase.</li>
            <li>You agree to use the website only for <strong>lawful</strong> purposes and in compliance with these terms.</li>
            <li>You are responsible for maintaining the confidentiality of your account and password.</li>
        </ul>

        <h2>3. User-Generated Content</h2>
        <p>By posting content on Chiase, you grant us a <strong>non-exclusive, worldwide, royalty-free license</strong> to display and share your content.</p>
        <p>You <strong>retain ownership</strong> of your content but grant us the right to store, display, and distribute it.</p>
        <p>You must not post <strong>illegal, harmful, or offensive content</strong>, including hate speech, violence, or explicit material.</p>

        <h2>4. Prohibited Activities</h2>
        <p>You agree <strong>NOT</strong> to:</p>
        <ul>
            <li>Use Chiase for <strong>spamming, phishing, or fraudulent activities</strong>.</li>
            <li>Upload viruses, malware, or any harmful code.</li>
            <li>Impersonate others or create fake accounts.</li>
            <li>Violate <strong>copyright laws</strong> by posting unauthorized content.</li>
            </ul>

            <h2>5. Account Termination</h2>
            <p>We reserve the right to <strong>suspend or terminate</strong> your account if you violate these Terms and Conditions.</p>

            <h2>6. Privacy & Data Collection</h2>
            <ul>
                <li>We collect and process user data in accordance with our <strong>Privacy Policy</strong>.</li>
                <li>We do not sell or share personal data with third parties <strong>without consent</strong>.</li>
            </ul>

            <h2>7. Limitation of Liability</h2>
            <p>Chiase is provided <strong>"as is"</strong> without any warranties.</p>
            <p>We are <strong>not liable</strong> for any losses, damages, or issues resulting from your use of the site.</p>

            <h2>8. Changes to These Terms</h2>
            <p>We may update these terms <strong>at any time</strong>, and it is your responsibility to check for changes. Continued use of Chiase means you accept the updated terms.</p>

            <h2>9. Contact Information</h2>
            <p>If you have any questions about these Terms, contact us at <strong>[your email]</strong>.</p>

            <p>By using Chiase, you agree to these Terms and Conditions. </p>
                    <div class="flex flex-row pt-4 justify-center gap-4">
                        <button type="button" onclick="closeTermsModal()" class="action-button">Close</button>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
    <script>
        const form = document.getElementById("signup-form");
        const clearModal = document.getElementById("clear-modal");
        const clearBtn = document.getElementById("clearBtn");
        const termsLink = document.getElementById("terms");
        const termsModal = document.getElementById("terms-modal");

        termsLink.addEventListener("click", (e) => {
            openTermsModal();
        });

        function openTermsModal() {
            termsModal.style.display = 'flex';
        }

        function closeTermsModal() {
            termsModal.style.display = 'none';
        }
        
        function openClearModal() {
            clearModal.style.display = 'flex';
        }

        function closeClearModal() {
            clearModal.style.display = 'none';
        }

        function clearInputs() {
            const inputs = document.querySelectorAll('#signup-form input:not([type="submit"])');
            inputs.forEach(input => {
                input.value = '';
            });
            clearModal.style.display = 'none';
        }

        function isValidEmail(email) {
            const hasAtSign = email.indexOf('@') > 0;
            const hasDot = email.lastIndexOf('.') > email.indexOf('@');
            const isValidLength = email.length > (email.lastIndexOf('.') + 1);
            
            return hasAtSign && hasDot && isValidLength;
        }

        form.addEventListener("submit", (event) => {
            const username = document.getElementById("username").value;
            const password = document.getElementById("password").value;
            const full_name = document.getElementById("full_name").value;
            const emailInput = document.getElementById("email").value;
            const age = document.getElementById("age").value;
            const repeatPassword = document.getElementById("repeat_password").value;
            const emailAlert = document.getElementById("email-alert");
            const ageAlert = document.getElementById("age-alert");
            const repeatAlert = document.getElementById("repeat-password-alert");
            const termsCheck = document.getElementById("terms-check");

            emailAlert.textContent = '';
            ageAlert.textContent = '';
            repeatAlert.textContent = '';
            let isValid = true;

            if (!username || !password || !full_name || !emailInput || !age || !repeatPassword) {
                alert("One or more required fields are empty.");
                isValid = false;
            }

            if (!isValidEmail(emailInput)) {
                emailAlert.textContent = "Email wrong format";
                isValid = false;
            }
            
            if (isNaN(age) || age <= 0) {
                ageAlert.textContent = "Age must be a number";
                isValid = false;
            }
            
            if (password != repeatPassword) {
                repeatAlert.textContent = "Password is not the same.";
                isValid = false;
            }

            if (!termsCheck.checked) {
                alert("Please agree to the terms and conditions.");
                isValid = false;
            }

            if (!isValid) {
                event.preventDefault();
            }
        })
    </script>
</body>
</html>