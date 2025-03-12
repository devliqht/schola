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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
    <div class="grid-container-full">
        <div class="grid-item-wrapper">
            <div class="grid-item">
            <form action="validation/response-signup.php" method="GET">
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
                        <input type="text" name="username" placeholder="Username" required/>
                    </div>
                    <div class="col">
                        <input type="email" name="email" placeholder="Email" required/>
                    </div>
                    <div class="col">
                        <input type="password" name="password" placeholder="Password" required/>
                    </div>
                    <div class="col">
                        <input type="text" name="full_name" placeholder="Full Name" required/>
                    </div>
                    <div class="col">
                        <input type="Submit" class="poppins-regular" id="submit" value="Sign Up">
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
                        <a href="index.php" class="text-sm inter-300">Back to login page</a>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</body>
</html>