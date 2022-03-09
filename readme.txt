#H2 Users credentials (username, password):

- User eins:
    User1111, User1111!

- User zwei:
    User1112, User1112!

- User drei:
    User1113, User1113!

- Silvan van Veen
    Kamelfluesterer, Sagtiev1! 


# H2 Authorization Token JWT:

Die Klasse Authorization.php steht einwandfrei. Beim erfolgreichen Login wird ein JWT Token erstellt (jwt library firebase) und 
im Body der Response an den Client zurückgegeben. Im Frontend wird dann das Token im Local Storage gespeichert. 
Bei einigen Requests vom Client ans Backend (z.B. Challenge erstellen oder Challenges abrufen) wird folglich dieses Token im 
HTTP Header mit key "Authorization" als value mitgegeben. 

Im Backend wird dieses Token authorisiert ( Authorize::authorizeToken() ). Ist der Vorgang erfolgreich wird der Request genehmigt.

Problem: Der ganze Authorization-Vorgang hat geklappt, wenn er mit Postman geprüft wurde. 
Jedoch mit dem React Frontend hat der Vorgang nicht geklappt. Anfragen vom React Frontend mit dem 'Authorization' Header lösten 
immer einen Cors Preflight Error aus, bei aber Postman nicht. 

Der Kollege Tom Niekerken aus Hamburg hatte bei seiner Abagbe dasselbe Problem und musste 
schlussendlich auf die Authorisierung verzichten.
Der Dozent Philip Braunen (SAE) konnte mir auch nicht weiterhelfen, bzw. hat irgendwann das Supportmeeting in der Mitte verlassen, 
mit dem Kommentar, er käme zurück, dies tat er danach aber nicht mehr. Die Hälfte der Supportzeit also ist er verschwunden. 

Infolge habe ich es dann mit der Klasse XHR.js probiert, welche bei jedem Request den Token in ein Cookie überführt. 
Das hat jedoch auch wieder denselben CORS-Fehler erzeugt. 

Schlussfolgerung: 

Da sowohl die localStorage-Methode als auch die Cookies-Methode den Authorization Header setzt, da Postman Requests 
in beiden Methoden am CORS Preflight Zaun hängen bleiben und da Anfragen ohne den Authorization Header immer durchgingen im Backend,
liegt das Problem definitiv irgendwo zwischen den React Frontend Requests mit einem Authorization Header und dem Backend. 

Persönlicher Kommentar:

Da die Vorlesung in PHP von John Frischmuth (SAE) sowohl die LocalStorage- als auch die Cookie-Methode zeigte, aber im Frontend 
kein React benutzte, sondern normales TypeScript, musste ich also selbst schauen. Ich hoffe, dieser Aufwand wird mir in der 
Bewertung zugutekommen und war nicht umsonst. Daher ist auch die ganze Authorize.php immer noch im Frontend. 
Mein Anspruch war es ja auch, eine möglichst sichere App zu bauen. Doch dies wurde mir 
aufgrund der Unterrichtsmaterialien nicht wirklich gewährleistet. 
Am besten wäre es gewesen, den Weg NodeJS zu gehen, da wären diese Probleme nicht aufgetreten. 
Wenn ich schon eine Vorlesug zu JWT hatte, will ich den auch benutzen und nicht meine ganze Arbeit wegwerfen müssen. Danke für die 
Kenntnisnahme. Um mit dem JWT weiterfahren zu können, habe ich einen Workaround gewählt (s. n. Kapitel).

Workaround

Um überhaupt Daten im Frontend zu bekommen und im Backend wiederum an die $user_id des eingeloggten Users zu gelangen, verzichte ich 
also widerwillig auf den JSON Web Token und speichere beim Login die $user_id in der superglobalen $_SESSION-Variable.

