<?php require_once 'api/config.php'; ?>
<?php 
    if (isset($_SESSION['role']) && isset($_SESSION['username'])) { 
        header("Location: pages/home.php");
    };
?>
<!DOCTYPE html>
<html>
<head>
    <title>CHI-ASE Forum</title>
    <link rel="stylesheet" href="css/utilities/reset.css" />
    <link rel="stylesheet" href="css/utilities/fonts.css" />
    <link rel="stylesheet" href="css/utilities/util-text.css" />
    <link rel="stylesheet" href="css/utilities/util-padding.css" />
    <link rel="stylesheet" href="css/utilities/inputs.css" />
    <link rel="stylesheet" href="css/index.css" />
    <link rel="stylesheet" href="css/form.css" />
    <link rel="stylesheet" href="css/colors.css" />
    <link rel="stylesheet" href="css/utilities/responsive.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://kit.fontawesome.com/1a04278299.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="grid-container-full">
        <div class="grid-item item1">
        <form action="validation/response-login.php" method="GET" class="login-form">
            <div class="form-wrapper p-4">
                <div class="row pb-2">
                <img class="logo" src="assets/logo.png" />
                </div>
                <div class="col pb-4">
                    <span style="display: flex; flex-direction: column;">
                    <h1 class="text-3xl inter-700 pb-2">Welcome back</h1>
                    <p class="text-sm inter-400">Don't have an account? <a href="signup.php" class="gradient-text">Create an account</a></p>
                    </span>
                </div>
                <div class="col">
                    <input type="text" name="username" placeholder="Username" class="text-base index"/>
                </div>
                <div class="col">
                    <input type="password" name="password" id="password" placeholder="Password" class="text-base index" required>
                </div>
                <?php
                if (isset($_GET['errors'])) {
                    $errors = $_GET['errors'];
                    if (is_array($errors)) {
                        echo "<div class='col'>";
                        foreach ($errors as $error) {
                            echo "<p class='text-sm inter-400 gradient-text'>" . htmlspecialchars($error) . "</p>"; // htmlspecialchars for security
                        }

                        echo "</div>";
                    }
                }
                ?>
                <div class="col">
                    <input type="Submit" class="poppins-regular index" id="submit" value="Login">
                    <!-- <a href="#need-help" class="text-sm inter-300">Need Help?</a> -->
                </div>
            </div>
        </form>
        </div>
        <div class="grid-item item2">

        </div>
    </div>
    <script>
          const passwordInput = document.getElementById('password');
            const togglePasswordButton = document.getElementById('togglePassword');
            const eyeIcon = document.getElementById('eyeIcon'); // Get the icon element

            togglePasswordButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');

            });
        </script>
</body>
</html>