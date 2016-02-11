<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Feedback form</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
<div id="container">
    <div id="form-main">
        <div id="form-div">
            <div id="form-message"></div>
            <form class="form" id="feedback-form">

                <p class="name">
                    <input name="name" type="text"
                           class="feedback-input" placeholder="Name"
                           id="name" required/>
                </p>

                <p class="email">
                    <input name="email" type="email" class="feedback-input" id="email"
                           placeholder="Email" required/>
                </p>

                <p class="text">
                    <textarea name="text" class="feedback-input" id="comment"
                              placeholder="Comment" required></textarea>
                </p>


                <div class="submit">
                    <input type="submit" value="SEND" id="button-blue" onclick="return sendFeedback();"/>
                    <div class="ease"></div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript" src="assets/script/script.js"></script>
</body>

</html>