<?php

session_start();

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}


include 'caterings.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $service_id = intval($_POST['service_id']);
  
    foreach ($caterings as $catering) {
        if ($catering['id'] === $service_id) {
            if (isset($_SESSION['cart'][$service_id])) {
                $_SESSION['cart'][$service_id]['quantity'] += 1;
            } else {
                $_SESSION['cart'][$service_id] = [
                    'name' => $catering['name'],
                    'price' => $catering['price'],
                    'quantity' => 1
                ];
            }
            break;
        }
    }
    
    header("Location: cart.php");
    exit();
}


$search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$filter_stars = isset($_GET['stars']) ? intval($_GET['stars']) : 0;
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;


$filtered_caterings = array_filter($caterings, function($catering) use ($search, $filter_stars, $min_price, $max_price) {
    $matches_search = empty($search) || 
                      (strpos(strtolower($catering['name']), $search) !== false) ||
                      (strpos(strtolower($catering['description']), $search) !== false);
    $matches_stars = $filter_stars === 0 || $catering['stars'] === $filter_stars;
    $matches_price = true;
    if ($min_price > 0 && $max_price > 0) {
        $matches_price = $catering['price'] >= $min_price && $catering['price'] <= $max_price;
    } elseif ($min_price > 0) {
        $matches_price = $catering['price'] >= $min_price;
    } elseif ($max_price > 0) {
        $matches_price = $catering['price'] <= $max_price;
    }
    return $matches_search && $matches_stars && $matches_price;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catering Services</title>
    <link href="./shop.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .card-cover {
            position: relative;
            background-size: cover;
            background-position: center;
            cursor: pointer; 
        }

        .card-cover::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5); 
            z-index: 1;
            border-radius: 0.75rem; 
        }

        .card-cover .d-flex {
            position: relative;
            z-index: 2; 
        }

        
        .card {
            min-height: 300px;
        }


        .star-rating {
            color: gold;
        }

     
        .cart-icon {
            position: relative;
        }

        .cart-count {
            position: absolute;
            top: -5px;
            right: -10px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }

    
        body {
            background-image: url('./photoss/image2.png'); 
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center center;
            background-color: #f8f9fa; 
        }

        
        .main-container {
            background-color: rgba(255, 255, 255, 0.9); 
            border-radius: 10px;
            padding: 20px;
        }

        
        .modal-content {
            border-radius: 15px;
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
        }

        .service-list {
            list-style-type: disc;
            padding-left: 20px;
        }
        #custom-cards{
            
        }

       
.card-cover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}


.card-cover:hover {
    transform: scale(1.05); 
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); 
}


    </style>
</head>
<header class="p-3 mb-3 border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-dark text-decoration-none">
          <img src="./photoss/logo.png" class="bi me-2" width="40" height="40" role="img" aria-label="Bootstrap">
            <use xlink:href="#bootstrap"></use>
    </img>
        </a>

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="./" class="nav-link px-2 link-secondary">Home</a></li>
          <li><a href="#" class="nav-link px-2 link-dark">Services</a></li>
          <li><a href="#" class="nav-link px-2 link-dark">Event</a></li>
          <li><a href="#" class="nav-link px-2 link-dark">About</a></li>
        </ul>

        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" method="GET" action="">
          <input type="search" name="search" class="form-control" placeholder="Search..." aria-label="Search" value="<?php echo htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES); ?>">
        </form>

        <div class="dropdown text-end me-3">
          <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
          </a>
          <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="#">New project...</a></li>
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Sign out</a></li>
          </ul>
        </div>

   
        <div class="cart-icon">
            <a href="cart.php" class="d-block link-dark text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                  <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L1.01 3.607 0.61 2H0.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.2-6H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                </svg>
                <?php if (!empty($_SESSION['cart'])): ?>
                    <span class="cart-count"><?php echo array_sum(array_column($_SESSION['cart'], 'quantity')); ?></span>
                <?php endif; ?>
            </a>
        </div>
      </div>
    </div>
  </header>

<body>
    <div class="main-container container px-4 py-5" id="custom-cards">
        <h2 class="pb-2 border-bottom">Catering Services</h2>
        
       
        <div class="container py-4">
            <form method="GET" action="" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="search" name="search" class="form-control" placeholder="Search by name or description..." value="<?php echo htmlspecialchars($search, ENT_QUOTES); ?>">
                </div>
                <div class="col-md-2">
                    <select name="stars" class="form-select">
                        <option value="0">Filter by Stars</option>
                        <option value="1" <?php if($filter_stars == 1) echo 'selected'; ?>>1 Star</option>
                        <option value="2" <?php if($filter_stars == 2) echo 'selected'; ?>>2 Stars</option>
                        <option value="3" <?php if($filter_stars == 3) echo 'selected'; ?>>3 Stars</option>
                        <option value="4" <?php if($filter_stars == 4) echo 'selected'; ?>>4 Stars</option>
                        <option value="5" <?php if($filter_stars == 5) echo 'selected'; ?>>5 Stars</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="min_price" class="form-control" placeholder="Min Price" min="0" value="<?php echo htmlspecialchars($_GET['min_price'] ?? '', ENT_QUOTES); ?>">
                </div>
                <div class="col-md-2">
                    <input type="number" name="max_price" class="form-control" placeholder="Max Price" min="0" value="<?php echo htmlspecialchars($_GET['max_price'] ?? '', ENT_QUOTES); ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>

            <?php if (empty($filtered_caterings)): ?>
                <p class="text-center">No catering services found.</p>
            <?php else: ?>
            <div class="row row-cols-1 row-cols-lg-3 align-items-stretch g-4 py-1">
                <?php foreach($filtered_caterings as $catering): ?>
                    <div class="col">
                      <div class="card card-cover h-100 overflow-hidden text-white bg-dark rounded-5 shadow-lg" 
                           style="background-image: url('<?php echo htmlspecialchars($catering['background_image'], ENT_QUOTES); ?>');" 
                           data-id="<?php echo intval($catering['id']); ?>">
                        <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                          <h2 class="mb-2 display-6 lh-1 fw-bold"><?php echo htmlspecialchars($catering['name'], ENT_QUOTES); ?></h2>
                          <?php if (!empty($catering['description'])): ?>
                              <p class="mb-3"><?php echo htmlspecialchars($catering['description'], ENT_QUOTES); ?></p>
                          <?php endif; ?>
                          <p class="mb-3"><strong>Price:</strong> $<?php echo number_format($catering['price'], 2); ?></p>
                          <ul class="d-flex list-unstyled mt-auto">
                            <li class="me-auto">
                              <img src="<?php echo htmlspecialchars($catering['user_image'], ENT_QUOTES); ?>" alt="User" width="32" height="32" class="rounded-circle border border-white">
                            </li>
                            <li class="d-flex align-items-center me-3">
                              <?php if ($catering['stars'] > 0): ?>
                                  <span class="star-rating">
                                      <?php for($i = 0; $i < $catering['stars']; $i++): ?>
                                          &#9733;
                                      <?php endfor; ?>
                                      <?php for($i = $catering['stars']; $i < 5; $i++): ?>
                                          &#9734;
                                      <?php endfor; ?>
                                  </span>
                              <?php else: ?>
                                  <small><?php echo htmlspecialchars($catering['location'], ENT_QUOTES); ?></small>
                              <?php endif; ?>
                            </li>
                            <li class="d-flex align-items-center">
                              <svg class="bi me-2" width="1em" height="1em">
                                  <use xlink:href="#calendar3"></use>
                              </svg>
                              <small><?php echo htmlspecialchars($catering['date'], ENT_QUOTES); ?></small>
                            </li>
                          
                            <li class="ms-3">
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="service_id" value="<?php echo intval($catering['id']); ?>">
                                    <button type="submit" name="add_to_cart" class="btn btn-success btn-sm" title="Add to Cart">
                                        &#43;
                                    </button>
                                </form>
                            </li>
                          </ul>
                        </div>
                      </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"></script>
</body>
</html>
