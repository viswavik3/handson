<?php
// Retrieve form data
$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$price = $_POST['price'];
$photo_name = $_FILES['photo']['name'];
$photo_tmp = $_FILES['photo']['tmp_name'];

// Save data to Azure SQL database
$server_name = "dbserver2299.database.windows.net";
$database_name = "HandsOnAssignmentDB";
$username = "viswa";
$password = "Asdfghjkl@123";
$conn = mysqli_init();
mysqli_real_connect($conn, $server_name, $username, $password, $database_name, 1433);
$query = "INSERT INTO products (ProductID, ProductName, Price, Photo) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "issb", $product_id, $product_name, $price, $photo_name);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);
mysqli_close($conn);
if($conn === false)
{
    die(print_r(sqlsrv_errors(), true));
}
if (!$stmt) {
    die(print_r(sqlsrv_errors(), true));
}


// Upload photo to Azure Blob Storage
$connection_string = "DefaultEndpointsProtocol=https;AccountName=handsonsac2299;AccountKey=O7SUNCufYYRjQZHuyVcQW5qtr7z+7VilWClf07LJMMdSCouVRLJkPiFyBHxskG4bB8e6WVj/y6Ly+AStwMUqRQ==;EndpointSuffix=core.windows.net";
$container_name = "photos";
$blob_name = $photo_name;
$blob_service_client = BlobServiceClient::fromConnectionString($connection_string);
$container_client = $blob_service_client->getContainerClient($container_name);
$blob_client = $container_client->getBlobClient($blob_name);
$options = new CreateBlobOptions();
$options->setContentType($_FILES['photo']['type']);
$blob_client->upload($photo_tmp, $options);

if(sqlsrv_execute($stmt)) {
    echo "Details saved successfully!";
} else {
    echo "Error: Could not save details.";
}
?>
