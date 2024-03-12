<?php
// Include database connection
include "connectdb.php";

// Define columns
$columns = array(
    0 => 'patient_first_name',
    1 => 'patient_last_name',
    2 => 'patient_email_address',
    3 => 'patient_phone_no',
    4 => 'email_verify'
);

// Fetch records
$query = "SELECT * FROM patient_table";
$totalData = mysqli_num_rows(mysqli_query($conn, $query));
$totalFiltered = $totalData;

// Search condition
if (!empty($_POST['search']['value'])) {
    $searchValue = $_POST['search']['value'];
    $query .= " WHERE patient_first_name LIKE '%" . $searchValue . "%' OR patient_last_name LIKE '%" . $searchValue . "%' OR patient_email_address LIKE '%" . $searchValue . "%' OR patient_phone_no LIKE '%" . $searchValue . "%' OR email_verify LIKE '%" . $searchValue . "%'";
    $totalFiltered = mysqli_num_rows(mysqli_query($conn, $query));
}

// Order by
$orderColumn = $columns[$_POST['order'][0]['column']];
$orderDirection = $_POST['order'][0]['dir'];
$query .= " ORDER BY " . $orderColumn . " " . $orderDirection;

// Limit
$start = $_POST['start'];
$length = $_POST['length'];
$query .= " LIMIT " . $start . ", " . $length;

// Fetch records again with limit and search conditions
$result = mysqli_query($conn, $query);

$data = array();
while ($row = mysqli_fetch_array($result)) {
    $sub_data = array();
    $sub_data[] = $row["patient_first_name"];
    $sub_data[] = $row["patient_last_name"];
    $sub_data[] = $row["patient_email_address"];
    $sub_data[] = $row["patient_phone_no"];
    $status = '';
    if($row["email_verify"] == 'Yes')
    {
        $status = '<span class="badge badge-success">Yes</span>';
    }
    else
    {
        $status = '<span class="badge badge-danger">No</span>';
    }
    $sub_data[] = $status;
    $sub_data[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["patient_id"].'"><i class="fa fa-eye"></i></button>
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["patient_id"].'"><i class="fa fa-edit"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["patient_id"].'"><i class="fa fa-times"></i></button>
			</div>
			';
    $data[] = $sub_data;
}

// Response
$json_data = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => intval($totalData),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
);

echo json_encode($json_data);
?>

