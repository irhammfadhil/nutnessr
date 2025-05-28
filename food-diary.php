<?php
// food-diary.php
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Food Diary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .food-item { display: flex; align-items: center; padding: 10px; border-bottom: 1px solid #ddd; }
        .food-item img { width: 50px; height: 50px; margin-right: 10px; border-radius: 50%; }
        .category-card { border-radius: 10px; padding: 15px; text-align: center; }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">
    <h2 class="text-center">Food Diary</h2>

    <!-- Tabs Navigasi -->
    <ul class="nav nav-pills nav-justified mt-3">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#chatbot">Chatbot</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#recipes">Recipes</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#nutrition">Nutrition</a></li>
    </ul>

    <div class="tab-content mt-4">
        <!-- Chatbot Section -->
        <div class="tab-pane fade show active" id="chatbot">
            <h5>Chat with Nutrition Assistant</h5>
            <div class="border p-3 bg-white" style="height: 300px; overflow-y: auto;">
                <p><strong>Bot:</strong> Hi! What do you want to know about your food?</p>
            </div>
            <input type="text" class="form-control mt-2" placeholder="Ask something...">
        </div>

        <!-- Recipes Section -->
        <div class="tab-pane fade" id="recipes">
            <h5>Healthy Recipes</h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="category-card bg-success text-white">
                        <h6>Salads</h6>
                        <p>38 healthy recipes</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="category-card bg-warning text-dark">
                        <h6>Veg Food</h6>
                        <p>27 healthy recipes</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="category-card bg-danger text-white">
                        <h6>Meat Dishes</h6>
                        <p>19 healthy recipes</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nutrition Section -->
        <div class="tab-pane fade" id="nutrition">
            <h5>Food Nutrition Info</h5>
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Search food...">
                <button class="btn btn-outline-secondary">Search</button>
            </div>
            <div class="food-item bg-white">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTVljL2s-Nd_VJdYU2cWSLecWYqrbQiiUdWQA&s" alt="Food">
                <div>
                    <strong>Coffee and Milk</strong> <br>
                    <small>219 kcal</small>
                </div>
            </div>
            <div class="food-item bg-white">
                <img src="https://listonic.com/phimageproxy/listonic/products/avocados.webp" alt="Food">
                <div>
                    <strong>Avocado</strong> <br>
                    <small>250 kcal</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
