<?php
function moveBillToBills($conn, $bill_id) {

    $conn->begin_transaction();

    try {
        // Fetch bill details
        $stmt = $conn->prepare("SELECT * FROM rejected_bills WHERE bill_id = ?");
        $stmt->bind_param("i", $bill_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $bill = $result->fetch_assoc();
        $stmt->close();

        // Insert into another table
        $stmt = $conn->prepare("
            INSERT INTO bills (
                bill_id, vendor_name, client_email, bill_date, amount, description, date, bill_time,
                days_late, department_decision, dept_date, dept_time, dept_remark, dept_ta_time, unit_decision, unit_date, unit_time, unit_remark, unit_ta_time,
                accounts_decision, accounts_date, accounts_time, accounts_remark, turnaround_time
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isssssssssssssssssssssss",
            $bill['bill_id'], $bill['vendor_name'], $bill['client_email'], $bill['bill_date'], $bill['Amount'],
            $bill['Description'], $bill['Date'], $bill['bill_time'], $bill['days_late'], $bill['department_decision'],
            $bill['dept_date'], $bill['dept_time'],$bill['dept_remark'], $bill['dept_ta_time'], $bill['unit_decision'], $bill['unit_date'], $bill['unit_time'], $bill['unit_remark'], $bill['unit_ta_time'], $bill['accounts_decision'], $bill['accounts_date'], $bill['accounts_time'],$bill['accounts_remark'], $bill['turnaround_time']
        );
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE bills SET department_decision = 'Pending' WHERE bill_id = ?");
        $stmt->bind_param("i",$bill_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE bills SET unit_decision = 'Pending' WHERE bill_id = ?");
        $stmt->bind_param("i",$bill_id);
        $stmt->execute();
        $stmt->close();
        
        $stmt = $conn->prepare("UPDATE bills SET accounts_decision = 'Pending' WHERE bill_id = ?");
        $stmt->bind_param("i",$bill_id);
        $stmt->execute();
        $stmt->close();


        $stmt = $conn->prepare("UPDATE bills SET dept_remark = '' WHERE bill_id = ?");
        $stmt->bind_param("i",$bill_id);
        $stmt->execute();
        $stmt->close();
        $stmt = $conn->prepare("UPDATE bills SET unit_remark = '' WHERE bill_id = ?");
        $stmt->bind_param("i",$bill_id);
        $stmt->execute();
        $stmt->close();
        $stmt = $conn->prepare("UPDATE bills SET accounts_remark = '' WHERE bill_id = ?");
        $stmt->bind_param("i",$bill_id);
        $stmt->execute();
        $stmt->close();

        // Deleting from rejected bills
        $stmt = $conn->prepare("DELETE FROM rejected_bills WHERE bill_id = ?");
        $stmt->bind_param("i", $bill_id);
        $stmt->execute();
        $stmt->close();

        // Commit the transfer
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Rollback the transfer on error
        $conn->rollback();
        return false;
    }
}
?>
