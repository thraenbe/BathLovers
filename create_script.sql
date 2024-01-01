drop table registred_class,rooms,subjects,student,other_events,recomended_subjects;
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
  Subject_id int ,
  Rooms int ,
    FOREIGN KEY  (user_name) references student(id),
    FOREIGN KEY  (Subject_id) references subjects(id),
    FOREIGN KEY  (Rooms) references rooms(id)
);
CREATE TABLE other_events (
    id SERIAL PRIMARY KEY ,
    user_name int,
    event_name varchar(30),
    time_start varchar(30),
    time_end varchar(30),
    category varchar(30),
    details varchar(30),
    FOREIGN KEY (user_name) references student(id)
);
CREATE TABLE recomended_subjects (
    user_id int,
    subject_id int,
    FOREIGN KEY (user_id) references student(id),
    FOREIGN KEY (subject_id) references subjects(id)
)