<?php

$Department = "
    CREATE TABLE Department(
        department_Name char(20),
        department_ID int,
        phone_number char(20),
        Primary Key (department_ID)
    )";

$Customer_Service = "
    CREATE TABLE Customer_Service(
        customer_service_ID int,
        Primary Key (customer_service_ID),
        FOREIGN KEY (customer_service_ID) REFERENCES Department(department_ID)
            on delete cascade
            -- on update cascade
    )";

$Room_Service = "
    CREATE TABLE Room_Service(
    room_service_ID int,
    Primary Key (room_service_ID),
    FOREIGN KEY (room_service_ID) REFERENCES Department (department_ID)
        on delete cascade
        -- on update cascade
)";


$Kitchen = "
    CREATE TABLE Kitchen(
    kitchen_ID int,
    Primary Key (kitchen_ID),
    FOREIGN KEY (kitchen_ID) REFERENCES Department (department_ID)
        on delete cascade
        -- on update cascade
)";

$RoomType = "
    CREATE TABLE RoomType(
    type char(1),
    price int,
    Primary Key (type)
)";


$HotelPlanType = "
    CREATE TABLE HotelPlanType (
    type char(1),
    price int,
    Primary Key (type)
)";

$Customer = "
    CREATE TABLE Customer (
    customer_ID integer,
    customer_name VARCHAR(100),
    customer_email VARCHAR(320),
    phone_number char(20),
    department_ID integer,
    password char(50),
    UNIQUE (customer_email),
    Primary Key (customer_ID),
    Foreign key (department_ID) references Customer_Service (customer_service_ID)
        on delete cascade
        -- on update cascade
)";

$Room = "
    CREATE TABLE Room (
    room_number int,
    type char(1),
    department_ID int,
    customer_ID int,
    Primary Key (room_number),
    Foreign key (type) references RoomType (type)
        on delete cascade,
        -- on update cascade,
    Foreign key (department_ID) references Room_Service (room_service_ID)
        on delete cascade,
    Foreign Key (customer_ID) references Customer (customer_ID)
        on delete cascade
)";

$HotelPlan = "
    CREATE TABLE HotelPlan (
    type char(1),
    start_date Date,
    customer_ID int,
    primary key (type, customer_ID),
    Foreign key (customer_ID) references Customer (customer_ID)
        on delete cascade
        -- on update cascade
)";

$Dish = "
    CREATE TABLE Dish (
    dish_ID integer,
    price integer,
    dish_name char(30),
    department_ID integer,
    UNIQUE (dish_name),
    Primary Key (dish_ID),
    Foreign key (department_ID) references Kitchen (kitchen_ID)
        on delete cascade
        -- on update cascade
)";

$Order_Dish = "
    CREATE TABLE Order_Dish (
    customer_ID int,
    dish_ID int,
    Primary Key (customer_ID, dish_ID),
    Foreign Key (customer_ID) references Customer (customer_ID)
        on delete cascade,
    Foreign Key (dish_ID) references Dish (dish_ID)
        on delete cascade
)";

$Storage = "
    CREATE TABLE Storage (
    storage_ID int,
    location char(15),
    department_ID int,
    Primary Key (storage_ID),
    Foreign key (department_ID) references Department (department_ID)
        on delete cascade
        -- on update cascade
)";

$Supplier = "
    CREATE TABLE Supplier (
    supplier_ID int,
    supplier_name char(20),
    phone_number char(20),
    Primary Key (supplier_ID)
)";


$Purchase_from = "
    CREATE TABLE Purchase_from(
    storage_ID int,
    supplier_ID int,
    product VARCHAR(50),
    PRIMARY KEY(storage_ID,supplier_ID, product),
    FOREIGN KEY(storage_ID) REFERENCES Storage (storage_ID)
        -- ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY(supplier_ID) REFERENCES Supplier (supplier_ID)
        -- On UPDATE CASCADE
        ON DELETE CASCADE
)";

// $Product = "
//     CREATE TABLE Product(
//     product_ID int,
//     product_name char(20),
//     Primary Key (product_ID)
// )";

// $Sell = "
//     CREATE TABLE Sell(
//     product_ID int,
//     supplier_ID int,
//     PRIMARY KEY(product_ID,supplier_ID),
//     FOREIGN KEY(product_ID) REFERENCES Product (product_ID)
//         -- On UPDATE CASCADE
//         ON DELETE CASCADE,
//     FOREIGN KEY(supplier_ID) REFERENCES Supplier (supplier_ID)
//         -- On UPDATE CASCADE
//         ON DELETE CASCADE
// )";