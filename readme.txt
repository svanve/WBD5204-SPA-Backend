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
HTTP Header mit key "Authorization" mitgegeben. 

Im Backend wird dieses Token autorisiert ( Authorize::authorizeToken() ). Ist der Vorgang erfolgreich wird der Request genehmigt.

