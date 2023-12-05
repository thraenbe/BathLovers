<?php
session_start();
include('../util/db.php');
include('../util/functions.php');

if (isset($_SESSION['user'])) {
    $username = $_SESSION['user'];
    $user_id = get_user_id($dbconn,$username);
    $row = 1;
    $subjects = get_subjects($dbconn,$row);
    $non_school_events = get_nonschool_events($dbconn,$user_id);
    echo "<h2>Courses:</h2>";
    ?>
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>course-information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">


    <script>
      var website = "https://sluzby.fmph.uniba.sk/infolist/SK/2-IKVa-115.html";
      var credits = "6 ECTS";
      var title = "math";
      var time1 = "Monday: 8:00 - 10:30";
      var time2 = "";
      var courseTag = "Lecture";
      var languageTag = "English";
      var teacher1 = "prof. Ing. Igor Farkas 	(prednášajúci)";
      var teacher2 = "RNDr. Kristína Malinovská, PhD. (cvičiaci)";
      var room = "I9";
      var code = "2-IKVa-115";
      var topic = "The course objectives are to make the students familiar with basic principles of various computational methods of data processing that can commonly be called computational intelligence (CI). This includes mainly bottom-up approaches to solutions of (hard) problems based on various heuristics (soft computing), rather than exact approaches of traditional artificial intelligence based on logic (hard computing). Examples of CI are nature-inspired methods (artificial neural networks, evolutionary algorithms, fuzzy systems), as well as probabilistic methods and reinforcement learning. After the course the students will be able to conceptually understand the important terms and algorithms of CI, and choose appropriate method(s) for a given task. The theoretical introduction is combined with practical examples.";


      function displayValues() {
        document.getElementById("credits").innerText = credits;
        document.getElementById("title").innerText = title;
        document.getElementById("time1").innerText = time1;
        document.getElementById("time2").innerText = time2;
        document.getElementById("courseTag").innerText = courseTag; 
        document.getElementById("languageTag").innerText = languageTag;
        document.getElementById("teacher1").innerText = teacher1;
        document.getElementById("teacher2").innerText = teacher2;
        document.getElementById("room").innerText = room;
        document.getElementById("code").innerText = code;
        document.getElementById("topic").innerText = topic; 
        document.getElementById("website").href = website ;
        document.getElementById("code").href = website;

      }

    </script>
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a id = "credits" class="btn btn-light navbar-brand" href="#">
                 ECTS
            </a>        
            <a class="btn btn-light navbar-brand" href="#">
                <img src="..\icons\icons\navbar\cancel.png" alt="Logo" class="d-inline-block align-text-top">
            </a>          
        </div>
    </nav>



  <div id="content" class="container-fluid">
      <div class=" d-flex justify-content-center">
        <h1 id="title">tut</h1>
      </div>
      
      <table  class="table table-hover">
          <tbody>
            <tr>
              <td ><a class="btn " href=""><img src="../icons/icons/event/time.png"  alt="wrong" class="d-inline-block align-text-top"></a></td>
              <td>
                <table>
                  <tr><td id="time1">  </td></tr>
                  <tr><td id="time2" > </td></tr>
                </table>
              </td>
            </tr>
            <tr>
              <td ><a class="btn " href=""><img src="../icons/icons/event/tag.png"  alt="wrong" class="d-inline-block align-text-top"></a></td>
               <td><table > 
                <tbody>
                  <td class="btn btn-info" id="courseTag"> </td>
                  <td class="btn btn-info"  id="languageTag"> </td>
                </tbody>
               </table></td> 
            </tr>
            <tr>
              <td ><a class="btn " href=""><img src="../icons/icons/event/participants.png"  alt="wrong" class="d-inline-block align-text-top"></a></td>
              <td>
                <table>
                  <tr><td id="teacher1">  </td></tr>
                  <tr><td id="teacher2" > </td></tr>
                </table>
              </td>
            </tr>
            <tr>
              <td ><a class="btn " href="roomplan.html"><img src="../icons/icons/event/location.png"  alt="wrong" class="d-inline-block align-text-top"></a></td>
              <td > <a id="room" class="btn btn-light" href="roomplan.html"></a></td>
            </tr>
            <tr>
              <td  ><a id="website" class="btn " href=""><img src="../icons/icons/side menu/3 day.png"  alt="wrong" class="d-inline-block align-text-top"></a></td>
              <td> <a  id="code" class="btn btn-link"></a> </td>
            </tr>
            <tr>
              <td ><a class="btn " href=""><img src="../icons/icons/event/note.png"  alt="wrong" class="d-inline-block align-text-top"></a></td>
              <td id="topic"> </td>
            </tr>
          </tbody>
        </table>
      <div class="text-center">
          <button id="addToSchedule" type="button" class="btn btn-primary " onclick="addToSchedule()">
          Add to Schedule
          </button>
      </div>


  </div>



  <nav class="navbar fixed-bottom navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
          <a class="btn btn-light navbar-brand" href="../calendar/weekly.html">
              <img src="../icons/icons/tabbar/calendar.png" alt="Logo" class="d-inline-block align-text-top">
          </a>        
          <a class="btn btn-light navbar-brand" href="../search/search.html">
              <img src="../icons/icons/tabbar/search.png" alt="Logo" class="d-inline-block align-text-top">
          </a>          
          <a class="btn btn-light navbar-brand" href="../setting/seetings.html">
              <img src="../icons/icons/tabbar/account.png" alt="Logo" class="d-inline-block align-text-top">
          </a>          
      </div>
  </nav>
    <script>
      displayValues();
      document.body.appendChild(website);
      function addToSchedule() {
        var button = document.getElementById("addToSchedule");
        if (button.innerHTML.trim() === "Add to Schedule") {
          button.innerHTML = "Remove from Schedule";
        } else {
          button.innerHTML = "Add to Schedule";
        }
      }
    </script>
  
    <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
    
</html>
<?php } ?>
       
</section>