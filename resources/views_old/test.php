<!DOCTYPE html>
<html>
<head>
    <style>
        .post-comments-container {
            position: relative;
            display: inline-block;
        }

        .post-comments {
            border: 2px solid #8D12D1;
            resize: none; /* Prevent textarea from being resized */
            padding: 10px;
            width: 300px;
            height: 150px;
        }

        .custom-button {
            position: absolute;
            bottom: 10px;
            right: 10px;
            padding: 5px 10px;
            background-color: #8D12D1;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="post-comments-container">
    <textarea name="comments" class="form-control post-comments"></textarea>
    <button class="custom-button">Submit</button>
</div>

</body>
</html>
