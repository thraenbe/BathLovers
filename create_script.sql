CREATE TABLE rooms (
  id int NOT NULL PRIMARY KEY ,
  name varchar(10) NOT NULL,
  name_en varchar(30) NOT NULL
) ;

CREATE TABLE subjects (
  id int NOT NULL PRIMARY KEY ,
  name varchar(30) NOT NULL,
  name_en varchar(30) NOT NULL,
  teacher varchar(30) NOT NULL,
  information_plan varchar(250) NOT NULL,
  time time NOT NULL
) ;
CREATE TABLE student (
  id int NOT NULL PRIMARY KEY ,
  user_name varchar NOT NULL,
  password varchar NOT NULL,
  specialization varchar(30) NOT NULL,
  year int NOT NULL
) ;
CREATE TABLE registred_class (
  user_name int NOT NULL,
  Subject int NOT NULL,
  Rooms int NOT NULL,
    FOREIGN KEY  (user_name) references student(id),
    FOREIGN KEY  (Subject) references subjects(id),
    FOREIGN KEY  (Rooms) references rooms(id)
);