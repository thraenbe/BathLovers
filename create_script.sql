drop table registred_class,rooms,subjects,student;
CREATE TABLE rooms (
  id SERIAL PRIMARY KEY ,
  name varchar(30) 
) ;

CREATE TABLE subjects (
  id SERIAL PRIMARY KEY ,
  name varchar(30) ,
  name_en varchar(30) ,
  teacher varchar(30) ,
  information_plan varchar(250) ,
  time_start varchar(30) ,
  time_end varchar(30) 
) ;
CREATE TABLE student (
  id SERIAL PRIMARY KEY ,
  user_name varchar  unique ,
  password varchar ,
  specialization varchar(30) ,
  year int 
) ;
CREATE TABLE registred_class (
  user_name int ,
  Subject int ,
  Rooms int ,
    FOREIGN KEY  (user_name) references student(id),
    FOREIGN KEY  (Subject) references subjects(id),
    FOREIGN KEY  (Rooms) references rooms(id)
);
