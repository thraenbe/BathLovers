import xml.etree.ElementTree as ET
import random
import datetime 

weekdays = ['Monday' , 'Tuesday', 'Wednesday' , 'Thursday' , 'Friday']
times = ['08:10' ,
'09:00' ,
'09:50' ,
'10:40' ,
'11:30' ,
'12:20' ,
'13:10' ,
'14:00' ,
'14:50' ,
'15:40' ,
'16:30' ,
'17:20' ,
'18:10' ,
'19:00' ,]

# Parse the XML file and get the root element
tree = ET.parse('INF.xml')
root = tree.getroot()

# Print the entire XML tree
# Iterate over all child elements
for child in root:
    print(child.tag )  #

information_list = root.find('informacneListy')

for info in information_list: # iterate through every course

    #language 
    pj_element = info.find('_PJ_')
    if pj_element is not None:
        texty_element = pj_element.find('texty')
        if texty_element is not None:
            p_element = texty_element.find('p')
            if p_element is not None:
                language = p_element.text
            else:
                language = ''
        else:
            language = ''
    else:
        language = ''


    teacher = info.find('vyucujuciAll').findall('vyucujuci')

    #WEBSITE
    url_element = info.find('_URL_')
    if url_element is not None:
        texty_element = url_element.find('texty')
        if texty_element is not None:
            p_element = texty_element.find('p')
            if p_element is not None:
                website = p_element.text
            else:
                website = ''
        else:
            website = ''
    else:
        website = ''

    topic = info.find('nazov').text
    credits = info.find('kredit').text
    code = info.find('skratka').text
    day = weekdays[random.randint(0,4)]
    time = random.randint(0,11)
    time = times[time] + ' - ' + times[time + 2]

    teachers = ''
    for line in teacher:
        teachers = teachers + '\n' + line.find('plneMeno').text

    # SUMMARY 
    so_element = info.find('_SO_')
    if so_element is not None:
        texty_element = so_element.find('texty')
        if texty_element is not None:
            summary = texty_element.findall('p')
        else:
            summary = []
    else:
        summary = []


    text = ''
    for line in summary:
        text = text + '\n' + line.text

    print(topic)
    print(credits)
    print(teachers)
    print(text)
    print(language)
    print(website)
    print(code)
    print(day)
    print(time)

