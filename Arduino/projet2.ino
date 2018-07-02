int incomingByte = 0;   // variable pour lecture de l'octet entrant
const int aimant =2;
const int ledv =3;
const int ledr =4;

void setup() {
  pinMode (aimant, OUTPUT) ;
 pinMode (ledv, OUTPUT) ;
 pinMode (ledr, OUTPUT) ;
  Serial.begin(9600);     // ouvre le port série et fixe le débit à 9600 bauds
}

void loop() {
  digitalWrite (aimant, HIGH) ;
  digitalWrite (ledr, HIGH) ;
  digitalWrite (ledv, LOW) ;
        // envoie une donnée sur le port série seulement quand reçoit une donnée
        if (Serial.available() > 0) { // si données disponibles sur le port série
                // lit l'octet entrant
                incomingByte = Serial.read();
                // renvoie l'octet reçu
                Serial.print("Octet recu: ");
                Serial.println(incomingByte, DEC);
                if (incomingByte==49){
                    digitalWrite (aimant,LOW);
                    digitalWrite (ledr, LOW) ;
                    digitalWrite (ledv, HIGH) ;
                    delay(15000);
                }
        }
}

