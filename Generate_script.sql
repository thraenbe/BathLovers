-- Insert into the Rooms
INSERT into rooms (name) values ('A'),('B'),('C'),('F1'),('F2');
WITH RECURSIVE NumberSequence AS (
    SELECT 1 AS num
    UNION ALL
    SELECT num + 1 FROM NumberSequence WHERE num < 99
)
INSERT INTO rooms (name)
SELECT
    prefix || '-' || digit1 || digit2 || num
FROM
    (SELECT unnest(ARRAY['F1', 'F2', 'M', 'I']) AS prefix) AS prefixes,
    (SELECT unnest(ARRAY['1', '2']) AS digit1) AS digit1_values,
    (SELECT unnest(ARRAY['1', '2', '3', '4', '5', '6', '7', '8', '9']) AS digit2) AS digit2_values,
    NumberSequence;

-- Insert into student
INSERT INTO student (user_name, password, specialization, year)
SELECT
    user_name,
    'password123' AS password, -- Replace with your password logic
    specialization,
    floor(random() * 5) + 1 AS year
FROM (
    SELECT
        first_name || last_name AS user_name,
        'password123' AS password,
        specialization,
        ROW_NUMBER() OVER (PARTITION BY first_name, last_name ORDER BY random()) as row_num
    FROM
        (SELECT unnest(ARRAY['John', 'Alice', 'Bob', 'Emma', 'David']) AS first_name) AS first_names,
        (SELECT unnest(ARRAY['Smith', 'Johnson', 'Williams', 'Jones', 'Brown']) AS last_name) AS last_names,
        (SELECT unnest(ARRAY['Application Informatics', 'Informatics', 'Mathematics', 'Financial Mathematics', 'Data Science', 'Insurance Mathematics', 'Theoretical Physics', 'Physics', 'Cognitive Science']) AS specialization) AS specializations
) AS numbered_rows
WHERE row_num = 1;

-- Display the data in the student table
SELECT * FROM student;

-- insert subjects
INSERT INTO subjects (name, name_en, teacher, information_plan, time_start, time_end)
VALUES
    ('Webove metodologie P','Web methodologies L','Homola','https://courses.matfyz.sk/home','Monday 10:40','Monday 12:10'),
    ('Webove metodologie C','Web methodologies E','Kluka','https://courses.matfyz.sk/home','Tuesday 11:30','Tuesday 13:00'),
    ('Numericka analyza P','Numerical analysis L','Babusikova','https://sluzby.fmph.uniba.sk/infolist/sk/2-AIN-114_14.html','Monday 12:20','Monday 13:50'),
    ('Numericka analyza C','Numerical analysis E','Babusikova','https://sluzby.fmph.uniba.sk/infolist/sk/2-AIN-114_14.html','Tuesday 13:00','Tuesday 14:40'),
    ('Matematicke modelovanie P','Mathematical modeling L','Durikovic','https://dai.fmph.uniba.sk/w/Course:Physical-based_Animations/sk#Student_Animation_Projects','Monday 16:30','Monday 18:00'),
    ('Matematicke modelovanie C','Mathematical modeling E','Durikovic','https://dai.fmph.uniba.sk/w/Course:Physical-based_Animations/sk#Student_Animation_Projects','Thursday 11:30','Thursday 13:00'),
    ('Rusky jazyk C','Russian language E','Victoria','https://fmph.uniba.sk/microsites/kjp/katedra-jazykovej-pripravy/mgr-viktoria-mirsalova/rusky-jazyk/','Monday 18:10','Monday 19:40'),
    ('Florbal','Floorball','Mokus','https://ktvs.fmph.uniba.sk/','Wednesday 13:30','Wednesday 14:30'),
    ('OS programovanie P','OS programming L','Siska','https://dai.fmph.uniba.sk/w/Course:OsProg/sk','Tuesday 09:50','Tuesday 11:20'),
    ('OS programovanie C','OS programming E','Siska','https://dai.fmph.uniba.sk/w/Course:OsProg/sk','Wednesday 08:10','Wednesday 09:40');
--     ('Matematika', 'Math', 'John Smith', 'https://www.mathplan.com', 'Monday 08:00', 'Monday 10:30'),
--     ('Fyzika', 'Physics', 'Jane Doe', 'https://www.physicsplan.com', 'Tuesday 10:15', 'Tuesday 12:45'),
--     ('Chémia', 'Chemistry', 'Michael Johnson', 'https://www.chemistryplan.com', 'Wednesday 13:30', 'Wednesday 16:00'),
--     ('Slovenčina', 'Slovak Language', 'Emily Wilson', 'https://www.slovaklanguageplan.com', 'Thursday 15:45', 'Thursday 18:15'),
--     ('Dejepis', 'History', 'David Brown', 'https://www.historyplan.com', 'Friday 08:30', 'Friday 11:00'),
--     ('SK','SR','koulek','www.sme.sk','Tuesday 11:00','Tuesday 13:30'),
--     ('EN','EN','kodulek','www.sme.sk','Tuesday 09:00','Tuesday 10:30');