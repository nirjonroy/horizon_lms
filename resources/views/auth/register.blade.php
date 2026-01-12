<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Horizon-Admin Register</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow-lg p-4 rounded-4" style="width: 400px;">
        <h3 class="text-center mb-4 text-primary">Create Account</h3>

        <form action="{{ route('register') }}" method="post">
            @csrf

            <!-- Name -->
            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" id="name" class="form-control" required placeholder="Enter your full name">
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required placeholder="Enter your email">
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Password</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Enter password">
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
                <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Confirm password">
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary w-100 py-2">Register</button>
        </form>

        <p class="text-center mt-3 small">
            Already have an account? <a href="{{ route('login') }}" class="text-decoration-none">Login</a>
        </p>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
