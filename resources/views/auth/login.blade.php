<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Aplikasi HRD</title>
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/x-icon"
        href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTIoMmTAmRH0abn7f5jb2sQvX8SOLsN2cCZ2A&s" />

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            height: 100vh;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #4070f4;
            padding: 20px;
        }

        .form {
            max-width: 400px;
            width: 100%;
            padding: 30px;
            border-radius: 8px;
            background: #FFF;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header {
            font-size: 32px;
            font-weight: 600;
            color: #232836;
            text-align: center;
            margin-bottom: 20px;
        }

        .form .field {
            margin-top: 15px;
            position: relative;
        }

        .field input,
        .field button {
            width: 100%;
            padding: 12px 15px;
            border-radius: 6px;
            border: 1px solid #CACACA;
            font-size: 16px;
        }

        .field input {
            outline: none;
            transition: border-color 0.3s ease;
        }

        .field input:focus {
            border-color: #0171d3;
        }

        .eye-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            font-size: 18px;
            color: #8b8b8b;
            cursor: pointer;
        }

        .field button {
            background-color: #0171d3;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .field button:hover {
            background-color: #005bb5;
        }

        .form-link {
            text-align: center;
            margin-top: 10px;
        }

        .form-link a {
            color: #0171d3;
            text-decoration: none;
        }

        .form-link a:hover {
            text-decoration: underline;
        }

        .image-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }

        .image-container img {
            height: 50px;
            width: 50px;
            border-radius: 50%;
        }

        @media (max-width: 768px) {
            header {
                font-size: 28px;
            }

            .form {
                padding: 20px;
            }

            .field input,
            .field button {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .form {
                padding: 15px;
            }

            .field input,
            .field button {
                font-size: 12px;
            }
        }
    </style>
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.15/dist/sweetalert2.min.css">

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.6.15/dist/sweetalert2.min.js"></script>
</head>

<body>
    @if (session('error'))
        <script>
            Swal.fire({
                html: '<strong>{{ session('error') }}</strong>',
                icon: 'error',
                title: 'Login Gagal!',
                text: "{{ session('error') }}",
                confirmButtonText: 'Ok',
            })
        </script>
    @endif
    <section class="container">
        <div class="form">
            <div class="image-container">
                <img src="{{ asset('assets/img/logos/image hr (1).jpg') }}" alt="logo">
            </div>
            <header>Login</header>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <input id="email" type="email" name="email" placeholder="Masukan Email Anda!" required>
                </div>
                <div class="field">
                    <input id="password" type="password" name="password" placeholder="Masukan Password Anda!" required>
                    <i class='bx bx-hide eye-icon' id="togglePassword"></i>
                </div>
                <div class="field">
                    <button type="submit">Login</button>
                </div>
            </form>
        </div>
    </section>

    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
            this.classList.toggle('bx-hide');
            this.classList.toggle('bx-show');
        });
    </script>
</body>

</html>
