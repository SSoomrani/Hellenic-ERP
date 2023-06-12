<?php
    function get_customer_names($conn) {
        $customer_id = array();
        $customer_names = array();
        $customer_identifiers = array();
        $query = $conn->query('SELECT id, forename, surname, email FROM customers');
        while($row = $query->fetch_assoc()) {
            $customer_identifiers[] = $row['id'];
            $customer_identifiers[] = $row['forename'];
            $customer_identifiers[] = $row['surname'];
            $customer_identifiers[] = $row['email'];
        }
        return $customer_identifiers;
    }


?>