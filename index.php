<!DOCTYPE html>
<html>
<head>
    <title>Làm bài tập</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }

        h1 {
            text-align: center;
            background-color: #4285f4;
            color: #fff;
            padding: 20px;
            margin: 0;
        }

        #chatbox {
            width: 80%;
            height: 350px;
            border: 1px solid #ccc;
            margin: 20px auto;
            overflow-y: auto;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
        }

        #chatbox p {
            margin: 15px 0;
            padding: 8px 12px;
            border-radius: 10px;
            font-size: 15px;
            line-height: 1.4;
            word-wrap: break-word;
        }

        #chatbox p.user {
            background-color: #DCF8C6;
            text-align: right;
            margin-left: 20%;
            border-bottom-right-radius: 2px;
        }

        #chatbox p.gemini {
            background-color: #E8EAED;
            text-align: left;
            margin-right: 20%;
            border-bottom-left-radius: 2px;
        }

        #chatbox p.gemini::before {
            content: "Gemini";
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #70757A;
            font-size: 12px;
        }

        #chatbox p.user::before {
            content: "Bạn";
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #70757A;
            font-size: 12px;
        }

        #chatbox p:hover {
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .preview {
            text-align: center;
            margin-top: 10px;
        }

        .preview img {
            max-width: 80%;
            max-height: 200px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        form {
            width: 80%;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap; /* Cho phép các phần tử xuống dòng */
            align-items: center; /* Căn giữa theo chiều dọc */
        }

        input[type="text"] {
            width: 70%; /* Điều chỉnh độ rộng ô nhập tin nhắn */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px; /* Thêm khoảng cách với ô file */
        }

        input[type="file"] {
            width: 30%; /* Điều chỉnh độ rộng ô chọn file */
            margin-bottom: 10px; /* Thêm khoảng cách với nút gửi */
        }

        button[type="submit"] {
            width: 100%; /* Nút gửi chiếm toàn bộ chiều rộng form */
            padding: 10px;
            background-color: #4285f4;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Làm bài tập</h1>

    <div id="chatbox">
    </div>

    <form id="messageForm" enctype="multipart/form-data">
        <input type="text" name="message" id="message" placeholder="Nhập tin nhắn của bạn...">
        <input type="file" name="image" id="image" accept="image/*">
        <button type="submit">Gửi</button>
        <div class="preview" id="imagePreview"></div>
    </form>

    <script>
        const form = document.getElementById('messageForm');
        const chatbox = document.getElementById('chatbox');
        const messageInput = document.getElementById('message');
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');

        imageInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    imagePreview.innerHTML = '';
                    imagePreview.appendChild(img);
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.innerHTML = '';
            }
        });

        form.addEventListener('submit', async function(event) {
            event.preventDefault();

            const message = messageInput.value;
            const imageFile = imageInput.files[0];

            // Tạo FormData object để gửi dữ liệu form
            const formData = new FormData();
            formData.append('message', message);
            if (imageFile) {
                formData.append('image', imageFile);
            }

            appendMessage('user', 'Bạn: ' + message);
            if (imageFile) {
              appendImage('user', URL.createObjectURL(imageFile))
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'chat.php');
            // Không cần thiết lập Content-Type khi sử dụng FormData
            // xhr.setRequestHeader('Content-Type', 'multipart/form-data');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    appendMessage('gemini', xhr.responseText);
                } else {
                    appendMessage('gemini', 'Lỗi: Không thể kết nối đến server.');
                }
            };
            xhr.send(formData);
        });

        function appendMessage(type, message) {
            const messageElement = document.createElement('p');
            messageElement.classList.add(type);
            messageElement.innerHTML = message;
            chatbox.appendChild(messageElement);
            chatbox.scrollTop = chatbox.scrollHeight;
        }
        function appendImage(type, image) {
            const messageElement = document.createElement('p');
            messageElement.classList.add(type);
            messageElement.innerHTML = "<img src='" + image + "' alt='Uploaded Image' width='200'/>";
            chatbox.appendChild(messageElement);
            chatbox.scrollTop = chatbox.scrollHeight;
        }
    </script>
</body>
</html>
