#H2 Users credentials (username, password):

- User eins:
    User1111, User1111!

- User zwei:
    User1112, User1112!

- User drei:
    User1113, User1113!

# H2 Authorization Token JWT:

Die Klasse Authorization.php steht einwandfrei. Beim erfolgreichen Login wird ein JWT Token erstellt (jwt library firebase) und 
im Body der Response an den Client zurückgegeben. Im Frontend wird dann das Token im Local Storage gespeichert. 
Bei einigen Requests vom Client ans Backend (z.B. Challenge erstellen oder Challenges abrufen) wird folglich dieses Token im 
HTTP Header mit key "Authorization" als value mitgegeben. 

Im Backend wird dieses Token authorisiert ( Authorize::authorizeToken() ). Ist der Vorgang erfolgreich wird der Request genehmigt.

Problem: Der ganze Authorization-Vorgang hat geklappt, wenn er mit Postman geprüft wurde. 
Jedoch mit dem React Frontend hat der Vorgang nicht geklappt. Anfragen vom React Frontend mit dem 'Authorization' Header lösten 
immer einen Cors Preflight Error aus, bei Postman aber nicht. 

