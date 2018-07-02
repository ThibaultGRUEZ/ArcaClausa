using Constellation;
using Constellation.Package;
using System;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Net;
using System.Text;
using System.Threading.Tasks;

namespace MyBrain
{
    public class Program : PackageBase
    {
        //initialisation des stateObject et des notifiers
        [StateObjectLink("MyBrain" 
            , "Values")]
        public StateObjectNotifier PushValues { get; set; }

        //initialisation des variables locales
        String pass = "";
        String login = "";
        Double latitude = 0;
        Double longitude = 0;
        int delay = 60;
        Guid myGuid = Guid.Empty;
        bool push = true; // unable pushBullet pop notification
        static void Main(string[] args)
        {
            PackageHost.Start<Program>(args);   
        }
        //message Callback de récupération des valeurs transmises par l'appli mobile
        [MessageCallback]
        public void UpdateValues(Double lat, Double lon, String login, String password)
        {
            var newValues = new Values();
            newValues.Latitude = lat;
            newValues.Longitude = lon;
            newValues.Login = login;
            newValues.Pass = password;
            PackageHost.PushStateObject("Values", newValues, lifetime: 50);
            PackageHost.WriteInfo("Initializing default settings, next reset in" + delay + "minutes");
            PackageHost.WriteInfo("Receiving app data");
        }
        public override void OnStart()
        {
            PackageHost.WriteInfo("Package starting - IsRunning: {0} - IsConnected: {1}", PackageHost.IsRunning, PackageHost.IsConnected);        
            Task.Factory.StartNew(async () =>
            {
                while (PackageHost.IsRunning)
                {
                    //réinitialise les valeurs des données toutes les minutes
                    var myData = new Values();
                    myData.Pass = "";
                    myData.Login = "";
                    myData.Latitude = 0;
                    myData.Longitude = 0;
                    PackageHost.PushStateObject("Values", myData, lifetime: 50);       
                    PackageHost.WriteInfo("Initializing default settings, reset in"+delay+"seconds");
                    await Task.Delay(60000*delay); //en minutes
                }
            }
            );
            PackageHost.WriteInfo("values are : {0}", this.PushValues.DynamicValue);

            //initialisation des valeurs des stateObjects au démarrage
            latitude = this.PushValues.DynamicValue.Latitude;
            longitude = this.PushValues.DynamicValue.Longitude;
            login = this.PushValues.DynamicValue.Login;
            pass = this.PushValues.DynamicValue.Pass;

            //notification des changements de valeurs*/
            this.PushValues.ValueChanged += OnValueChanged;    
        }

        private void reset()
        {
            PackageHost.SendMessage(MessageScope.Create("ConstellationPackagePython2"), "reset", null);
        }
        private void OnValueChanged(object sender, StateObjectChangedEventArgs e)
        {
            //récupération des nouvelles valeurs
            PackageHost.WriteInfo("values are : {0}", this.PushValues.DynamicValue);
            latitude = e.NewState.DynamicValue.Latitude;
            longitude = e.NewState.DynamicValue.Longitude;
            login = e.NewState.DynamicValue.Login;
            pass = e.NewState.DynamicValue.Pass;
            myGuid = Auth(pass, login, latitude, longitude);
           // myGuid = Guid.NewGuid();
            String myGuidString = myGuid.ToString();
            PackageHost.WriteInfo("generated token : {0}", myGuid);
            PackageHost.WriteInfo("Sending generated token");
          
            if (myGuid != Guid.Empty) //si token valide généré par Auth, envoi à la Raspberry
            {
                //communication avec la Raspberry
                MessageScope.Create("ConstellationPackagePython2").OnSagaResponse((response) =>
                 {
                     //premier envoi du token, récupération de ce même token
                     PackageHost.WriteInfo("Sending generated token");
                     PackageHost.WriteInfo("Raspberry response : {0}", response);
                     String responseC = myGuidString + "fr42kilj8";  //deuxième envoi du token + clé de sécurite
                     PackageHost.WriteInfo(" Sending token + key ");
                     PackageHost.SendMessage(MessageScope.Create("ConstellationPackagePython2"), "reponse", responseC);
                     // PackageHost.SendMessage(MessageScope.Create("PushBullet"), "PushNote", new object[] { "Safe Opening Request", "Coffre dévérouillé" });
                     
                 }).GetProxy().question<String>(myGuidString);
            }
        }
        //fonction d'authentification
        private Guid Auth(String pass, String login, Double latitude, Double longitude)
        {
            //communication avec le serveur
            PackageHost.WriteInfo("Sending data to server");
            WebRequest request = WebRequest.Create("http://192.168.137.121/phpproj18/test.php");  
            request.Method = "POST";  
            //envoi données au serveur + identité du package
            string postData = "login="+login+"&passwd="+pass+"&latitude="+latitude+"&longitude="+longitude+"&sender=123";
            byte[] byteArray = Encoding.UTF8.GetBytes(postData); 
            request.ContentType = "application/x-www-form-urlencoded";
            request.ContentLength = byteArray.Length;
            Stream dataStream = request.GetRequestStream();
            dataStream.Write(byteArray, 0, byteArray.Length); 
            dataStream.Close();  
            WebResponse response = request.GetResponse();
            Console.WriteLine(((HttpWebResponse)response).StatusDescription); 
            dataStream = response.GetResponseStream();  
            StreamReader reader = new StreamReader(dataStream);
            //récupération de la réponse serveur
            string responseFromServer = reader.ReadToEnd(); 
            Console.WriteLine(responseFromServer);

            //cas selon la réponse du serveur
            if (responseFromServer == "\nyoushallpass")
            {
                //accès authorisé, génération d'un token valide
                Guid g;
                g = Guid.NewGuid();
                Console.WriteLine(g);
                if (push == true) { PackageHost.SendMessage(MessageScope.Create("PushBullet"), "PushNote", new object[] { "Safe Opening Request", "Access Granted" }); }
                PackageHost.WriteInfo("Server response : Access Granted");
                reader.Close();
                dataStream.Close();
                response.Close();
                return g;
            }
            if (responseFromServer == "\nlogin")
            {
                //erreur de login, génération d'un token nul
                Guid l = Guid.Empty; ;
                Console.WriteLine(l);
                PackageHost.WriteInfo("Server response : Wrong Credentials, access denied");
                if (push == true) { PackageHost.SendMessage(MessageScope.Create("PushBullet"), "PushNote", new object[] { "Safe Opening Request", "Wrong Credentials, access denied" }); }
                reader.Close();
                dataStream.Close();
                response.Close();
                //reset();
                return l;
            }
            if (responseFromServer == "\ncoord")
            {
                //erreur de position, génération d'un token nul
                Guid g = Guid.Empty; ;
                Console.WriteLine(g);
                PackageHost.WriteInfo("Server response : Wrong location, access denied");
                if (push) { PackageHost.SendMessage(MessageScope.Create("PushBullet"), "PushNote", new object[] { "Safe Opening Request", "Wrong Location, access denied" }); }
                reader.Close();
                dataStream.Close();
                response.Close();
                //reset();
                return g;
            }
            if (responseFromServer == "\nlock")
            {
                //système verouillé par l'administrateur, génération d'un token nul
                Guid q = Guid.Empty; ;
                Console.WriteLine(q);
                PackageHost.WriteInfo("Server response : System Locked, access denied");
                if (push == true) { PackageHost.SendMessage(MessageScope.Create("PushBullet"), "PushNote", new object[] { "Safe Opening Request", "System Locked by admin, access denied" }); }
                reader.Close();
                dataStream.Close();
                response.Close();
                //reset();
                return q;
            }
            //autre cas
            reader.Close();
            dataStream.Close();
            response.Close();
            Console.WriteLine(responseFromServer);
            Guid w = Guid.NewGuid();
            //Guid w = Guid.Empty;
            Console.WriteLine(w);
            PackageHost.WriteInfo("Server response : granted");
            if (push == true) { PackageHost.SendMessage(MessageScope.Create("PushBullet"), "PushNote", new object[] { "Safe Opening RequeST", "Access granted" }); }
            //reset();
            return w;
        } 
    }
}
