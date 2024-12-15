<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Credit Card</title>
    <style>
        /* Add your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .center {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .card {
            width: 320px;
            height: 190px;
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
            color: #fff;
            border-radius: 15px;
            perspective: 1000px;
        }
        .flip {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.5s;
        }
        .flip:hover {
            transform: rotateY(180deg);
        }
        .front, .back {
            width: 100%;
            height: 100%;
            position: absolute;
            backface-visibility: hidden;
            border-radius: 15px;
        }
        .front {
            background: linear-gradient(135deg, #ff758c, #ff7eb3);
        }
        .back {
            background: #333;
            transform: rotateY(180deg);
        }
        .strip-top, .strip-bottom {
            position: absolute;
            width: 100%;
            height: 30px;
            background: rgba(255, 255, 255, 0.2);
        }
        .strip-top {
            top: 0;
        }
        .strip-bottom {
            bottom: 0;
        }
        .logo {
            position: absolute;
            top: 15px;
            left: 15px;
        }
        .chip {
            width: 50px;
            height: 40px;
            background: #fff;
            position: absolute;
            top: 70px;
            left: 15px;
            border-radius: 5px;
        }
        .card-number {
            position: absolute;
            bottom: 60px;
            left: 15px;
            font-size: 18px;
            letter-spacing: 2px;
        }
        .end {
            position: absolute;
            bottom: 40px;
            left: 15px;
            font-size: 12px;
        }
        .card-holder {
            position: absolute;
            bottom: 20px;
            left: 15px;
            font-size: 14px;
            text-transform: uppercase;
        }
        .master {
            position: absolute;
            bottom: 15px;
            right: 15px;
            display: flex;
        }
        .master .circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: red;
            margin-left: -5px;
        }
        .master .master-yellow {
            background: yellow;
        }
    </style>
</head>
<body>
    <div class="center">
        <div class="card">
            <div class="flip">
                <!-- Front Side -->
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
                        <?php echo isset($userName) ? $userName : 'John Doe'; ?>
                    </div>
                    <div class="master">
                        <div class="circle master-red"></div>
                        <div class="circle master-yellow"></div>
                    </div>
                </div>
                <!-- Back Side -->
                <div class="back">
                    <div class="strip-black"></div>
                    <div class="ccv">
                        <label>ccv</label>
                        <div>123</div>
                    </div>
                    <div class="terms">
                        <p>This card is property of Monzo Bank, Wonderland. Misuse is a criminal offence. If found, please return to Monzo Bank or to the nearest bank with a MasterCard logo.</p>
                        <p>Use of this card is subject to the credit card agreement.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
