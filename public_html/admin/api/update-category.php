<?php
require_once('/home/puandeks.com/backend/config.php');

header('Content-Type: application/json');

/*
|--------------------------------------------------------------------------
| Update Category (Name + Icon)
|--------------------------------------------------------------------------
| This endpoint updates an existing category.
| It supports updating:
| - category name
| - font-awesome icon class
|
| Expected POST (application/x-www-form-urlencoded):
| - id          : category id (int)
| - name        : new category name (string)
| - icon_class  : selected font-awesome class (string, optional)
|
| Example:
| id=12&name=Restaurants&icon_class=fas fa-utensils
|--------------------------------------------------------------------------
*/

// Validate inputs
$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$newName = isset($_POST['name']) ? trim($_POST['name']) : '';
$iconClass = isset($_POST['icon_class']) ? trim($_POST['icon_class']) : '';

if ($id <= 0 || $newName === '') {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request parameters."
    ]);
    exit;
}

try {
    /*
    |-------------------------------------------------------------
    | Update category record
    |-------------------------------------------------------------
    | icon_class is nullable, so it can be empty if not selected
    */
    $stmt = $pdo->prepare("
        UPDATE categories 
        SET name = :name, icon_class = :icon_class
        WHERE id = :id
    ");

    $stmt->execute([
        ':name'       => $newName,
        ':icon_class' => $iconClass !== '' ? $iconClass : null,
        ':id'         => $id
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Category updated successfully."
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to update category."
    ]);
}
