<!DOCTYPE html>
<html>
<head>
    <title>CHI-ASE Forum</title>
    <link rel="stylesheet" href="css/utilities/reset.css" />
    <link rel="stylesheet" href="css/utilities/fonts.css" />
    <link rel="stylesheet" href="css/utilities/util-text.css" />
    <link rel="stylesheet" href="css/utilities/util-padding.css" />
    <link rel="stylesheet" href="css/index.css" />
    <link rel="stylesheet" href="css/form.css" />
    <link rel="stylesheet" href="css/colors.css" />
    <link rel="stylesheet" href="css/admin.css" />
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
            background: var(--base-bg-two);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
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
    </style>
</head>
<body>
    <div class="grid-container-full">
        <div class="grid-item-wrapper">
            <div class="grid-item">
            <form id="signup-form" method="GET" action="validation/response-signup.php">
                <div class="form-wrapper p-4">
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
            </div>
        </div>
    </div>
    <script>
        const form = document.getElementById("signup-form");
        const clearModal = document.getElementById("clear-modal");
        const clearBtn = document.getElementById("clearBtn");
        
        function openClearModal() {
            clearModal.style.display = 'block';
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

            emailAlert.textContent = '';
            ageAlert.textContent = '';
            repeatAlert.textContent = '';
            let isValid = true;

            if (!username || !password || !full_name || !email || !age || !repeatPassword) {
                event.preventDefault();
                alert("One or more required fields are empty.");
                isValid = false;
                return;
            }

            if (!isValidEmail(emailInput)) {
                event.preventDefault();
                emailAlert.textContent = "Email wrong format";
                isValid = false;
            }
            
            if (isNaN(age) || age <= 0) {
                event.preventDefault();
                ageAlert.textContent = "Age must be a number";
                isValid = false;
            }
            
            if (password != repeatPassword) {
                event.preventDefault(); 
                repeatAlert.textContent = "Password is not the same.";
                isValid = false;
            }

            if (isValid) {
                form.submit();
            }
        })
    </script>
</body>
</html>