-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 20, 2026 at 09:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `d_storeltd`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `action_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(100) NOT NULL,
  `customer_name` varchar(200) NOT NULL,
  `address` varchar(200) NOT NULL,
  `phone_number` int(10) NOT NULL,
  `Created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `customer`
--
DELIMITER $$
CREATE TRIGGER `trg_customer_insert` AFTER INSERT ON `customer` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Inserted customer : ' , NEW.customer_name , 'phone:' , NEW.phone_number),'customer')
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_customer_update` AFTER UPDATE ON `customer` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Updated customer : ' , NEW.customer_name),'customer')
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_customer_delete` AFTER DELETE ON `customer` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Deleted customer : ' , OLD.customer_name),'customer')
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `position` varchar(50) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `employees`
--
DELIMITER $$
CREATE TRIGGER `trg_employees_insert` AFTER INSERT ON `employees` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , 'Inserted employee' , 'employees')
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_employees_update` AFTER UPDATE ON `employees` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Updated employee : ' , NEW.full_name) , 'employees')
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_employees_delete` AFTER DELETE ON `employees` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Deleted employee : ' , OLD.full_name) , 'employees')
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` date NOT NULL DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `trg_orders_insert` AFTER INSERT ON `orders` FOR EACH ROW BEGIN 
    INSERT INTO audit_log(username, action, table_name) 
    VALUES (CURRENT_USER(), 'Inserted Orders', 'orders');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_orders_update` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN 
    INSERT INTO audit_log(username, action, table_name) 
    VALUES (CURRENT_USER(), CONCAT('Updated Order - Amount: ', NEW.total_amount), 'orders');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_orders_delete` AFTER DELETE ON `orders` FOR EACH ROW BEGIN 
    INSERT INTO audit_log(username, action, table_name) 
    VALUES (CURRENT_USER(), CONCAT('Deleted Order - Amount: ', OLD.total_amount), 'orders');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_details_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(10) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `order_details`
--
DELIMITER $$
CREATE TRIGGER `trg_order_details_insert` AFTER INSERT ON `order_details` FOR EACH ROW BEGIN 
    INSERT INTO audit_log(username, action, table_name) 
    VALUES (CURRENT_USER(), 'Inserted order details', 'order details');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_order_details_update` AFTER UPDATE ON `order_details` FOR EACH ROW BEGIN 
    INSERT INTO audit_log(username, action, table_name) 
    VALUES (CURRENT_USER(), CONCAT('Updated order details - Qty: ', NEW.quantity), 'order details');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_order_details_delete` AFTER DELETE ON `order_details` FOR EACH ROW BEGIN 
    INSERT INTO audit_log(username, action, table_name) 
    VALUES (CURRENT_USER(), CONCAT('Deleted order details - Qty: ', OLD.quantity), 'order details');
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(100) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `categories` varchar(50) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `Created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `products`
--
DELIMITER $$
CREATE TRIGGER `trg_products_insert` AFTER INSERT ON `products` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Inserted Product : ' , NEW.product_name) , 'products')
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_products_update` AFTER UPDATE ON `products` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Updated Product : ' , NEW.product_name , ' - Price: ', NEW.price , ', Qty: ' , NEW.quantity) , 'products')
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_products_delete` AFTER DELETE ON `products` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Deleted Product : ' , OLD.product_name) , 'products')
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(60) NOT NULL,
  `role` varchar(20) NOT NULL,
  `Created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `user`
--
DELIMITER $$
CREATE TRIGGER `trg_user_insert` AFTER INSERT ON `user` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Inserted User : ' , NEW.user_name) , 'user')
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_user_update` AFTER UPDATE ON `user` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Updated User : ' , NEW.user_name , ' - Role: ', NEW.role) , 'user')
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_user_delete` AFTER DELETE ON `user` FOR EACH ROW INSERT INTO audit_log(username , action , table_name) VALUES (CURRENT_USER() , CONCAT('Deleted User : ' , OLD.user_name) , 'user')
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_details_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_details_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
