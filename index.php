<?php include_once 'resource/session.php'; ?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="resource/css/style.css">
    <link rel="stylesheet" href="resource/css/custom.css">
    <link rel="stylesheet" href="resource/css/credit-card.css">


    <title>Homepage</title>
</head>
<body>
<?php include 'partials/navbar.php'; ?>
    <!-- Page Content -->
    <div class="container">
        <h2>DC Bank</h2><hr>
        <p>REMEMBER: use your username that you set at signup to login and relogin after signing up with 
            your email.
        </p>
        <p> If you want to see a list of current members (active users), go to "My Profile"
            and you will see the link "Members" that allows you to view all active users.</p>

        <?php if (!isset($_SESSION['username'])): ?>
            <p>You are currently not signed in. <a href="login.php">Login</a> Not yet a member? <a href="signup.php">Signup</a></p>
        <?php else: ?>
            <p>You are logged in as <?php if (isset($_SESSION['username'])) echo $_SESSION['username']; ?>. <a href="logout.php">Logout</a></p>
        <?php endif; ?>

        <!-- Credit Card Section
        <h3>Credit Card Preview</h3>
        <div class="center">
            <div class="card">
                <div class="flip">
                    Front Side
                    <div class="front">
                        <div class="strip-top"></div>
                        <div class="strip-bottom"></div>
                        <svg class="logo" width="40" height="40" viewbox="0 0 17.5 16.2">
                            <path d="M3.2 0l5.4 5.6L14.3 0l3.2 3v9L13 16.2V7.8l-4.4 4.1L4.5 8v8.2L0 12V3l3.2-3z" fill="white"></path>
                        </svg>
                        <div class="chip"></div>
                        <div class="card-number">
                            <div class="section">5453</div>
                            <div class="section">2000</div>
                            <div class="section">0000</div>
                            <div class="section">0000</div>
                        </div>
                        <div class="end">
                            <span class="end-text">exp. end:</span>
                            <span class="end-date">11/22</span>
                        </div>
                        <div class="card-holder">
                            <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'John Doe'; ?>
                        </div>
                        <div class="master">
                            <div class="circle master-red"></div>
                            <div class="circle master-yellow"></div>
                        </div>
                    </div>
                    Back Side
                    <div class="back">
                        <div class="strip-black"></div>
                        <div class="ccv">
                            <label>ccv</label>
                            <div>123</div>
                        </div>
                        <div class="terms">
                            <p>This card is property of DC Bank, Wonderland. Misuse is a criminal offence. If found, please return to DC Bank or to the nearest bank with a MasterCard logo.</p>
                            <p>Use of this card is subject to the credit card agreement.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        End Credit Card Section
    </div> -->

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
