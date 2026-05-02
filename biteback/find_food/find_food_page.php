<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['customer_id'])) {
    header("Location: ../customer_login.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Find Food - BiteBack</title>
<link rel="stylesheet" href="find_food.css">
</head>

<body>

<!-- Header -->
<div class="header">

    <div class="logo">
        <img src="../logo.png" alt="BiteBack Logo" class="logo_img">

        <a href="../index.html" class="logo_name">
            <span style="color:black;">Bite</span>
            <span style="color:green;">Back</span>
        </a>
    </div>

    <div class="menu_toggle" onclick="toggleMenu()">☰</div>

    <div class="nav" id="navMenu">
        <a href="find_food_page.php" class="nav1">Find Food</a>
        <a href="../customer_reservations.php" class="nav1">My Reservations</a>
        <a href="../customer_logout.php" class="nav2">Logout</a>
    </div>

</div>


<!-- Content -->
<div class="content">

    <h1>Available Near You</h1>
    <p>Fresh food, ready to reserve. First come, first served.</p>

    <div class="foodgrid" id="foodgrid">
        <p>Loading nearby food...</p>
    </div>

</div>

</body>


<script>
function escapeHtml(text) {
    return text
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

const hasLocation = <?php echo (isset($_SESSION['lat']) && isset($_SESSION['lng'])) ? 'true' : 'false'; ?>;

function renderFoodList(data) {
    let output = "";

    console.log("renderFoodList called with data:", data);

    if (!Array.isArray(data)) {
        output = `<h2>${data.message || 'Unable to load food listings.'}</h2>`;
    } else if (data.length === 0) {
        output = "<h2>No food available near you right now 😔</h2>";
    } else {
        data.forEach(item => {
            const imageSrc = item.image ? `../${item.image}` : '../logo.png';
            console.log("Rendering item:", item);
            
            const isSoldOut = item.quantity <= 0;
            const buttonHtml = isSoldOut 
                ? '<button class="btn sold-out" disabled>Sold Out</button>'
                : `<button class="btn" onclick="reserveFood(${item.id}, '${escapeHtml(item.food_name)}', ${item.discounted_price})">Reserve Now</button>`;
            
            const quantityText = isSoldOut ? 'Sold Out' : `🍛 Qty: ${item.quantity}`;
            
            output += `
            <div class="card ${isSoldOut ? 'sold-out-card' : ''}">
                <img src="${imageSrc}" alt="${escapeHtml(item.food_name)}">
                <div class="card-body">
                    <div class="food-name">${escapeHtml(item.food_name)}</div>
                    <div class="price">
                        <span class="old-price">₹${item.price}</span>
                        <span class="new-price">₹${item.discounted_price}</span>
                    </div>
                    <div class="info">👤 ${escapeHtml(item.name)}</div>
                    <div class="info">📞 ${escapeHtml(item.mobile)}</div>
                    <div class="info">📍 ${escapeHtml(item.location)}</div>
                    <div class="info">⏰ Pickup: ${escapeHtml(item.pickup_time)}</div>
                    <div class="info ${isSoldOut ? 'sold-out-text' : ''}">${quantityText}</div>
                    <div class="info distance">📏 ${item.distance} KM Away</div>
                    ${buttonHtml}
                </div>
            </div>
            `;
        });
    }

    console.log("Final output length:", output.length);
    document.getElementById("foodgrid").innerHTML = output;
}

function fetchFoodItems() {
    fetch("find_food.php")
    .then(response => response.json())
    .then(data => renderFoodList(data))
    .catch(error => {
        document.getElementById("foodgrid").innerHTML = "<h2>Unable to load food listings.</h2>";
        console.log(error);
    });
}

function requestLocationAndLoadFood() {
    document.getElementById("foodgrid").innerHTML = "<h2>Detecting your location...</h2>";

    if (!navigator.geolocation) {
        document.getElementById("foodgrid").innerHTML = "<h2>Geolocation is not supported by your browser.</h2>";
        return;
    }

    navigator.geolocation.getCurrentPosition(function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;

        fetch(`find_food.php?lat=${lat}&lng=${lng}`)
            .then(response => response.json())
            .then(result => {
                if (result.status === 'ok') {
                    fetchFoodItems();
                } else {
                    document.getElementById("foodgrid").innerHTML = `<h2>${result.message || 'Unable to detect location.'}</h2>`;
                }
            })
            .catch(error => {
                document.getElementById("foodgrid").innerHTML = "<h2>Unable to save location.</h2>";
                console.error(error);
            });
    }, function() {
        document.getElementById("foodgrid").innerHTML = "<h2>Please allow location access to see nearby food.</h2>";
    });
}

if (hasLocation) {
    fetchFoodItems();
} else {
    requestLocationAndLoadFood();
}

function reserveFood(foodId, foodName, price) {
    console.log("Reserve button clicked: foodId=" + foodId + ", foodName=" + foodName + ", price=" + price);
    
    if (confirm(`Do you want to reserve "${foodName}" for ₹${price}?`)) {
        console.log("User confirmed. Sending POST to ../reserve_food.php");
        
        fetch('../reserve_food.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                
            })
        })
        .then(response => {
            console.log("Response status:", response.status);
            return response.json();
        })
        .then(data => {
            console.log("Response data:", data);
            if (data.success) {
                alert('Reservation successful! Contact the lister for pickup details.');
                location.reload();
            } else {
                alert('Reservation failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An error occurred while reserving the food: ' + error.message);
        });
    }
}
</script>


<script>
function toggleMenu()
{
    document.getElementById("navMenu").classList.toggle("active");
}
</script>

</html>