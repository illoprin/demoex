```sql
create table roles(
  id int primary key auto_increment,
  name varchar(64) not null
);
insert into roles(name) values ('user'), ('admin');
```

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    foreign key (role_id) references roles(id)
);
```

```sql
create table statuses(
    id INT PRIMARY KEY AUTO_INCREMENT,
    name varchar(60) not null
);
insert into statuses(name) values ('pending'), ('confirmed'), ('rejected');
```

```sql
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    booking_date DATE NOT NULL,
    booking_time TIME NOT NULL,
    guests_count INT NOT NULL,
    contact_phone VARCHAR(20) NOT NULL,
    status_id int not null,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    foreign key (status_id) references statuses(id)
);
```
