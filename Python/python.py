import Constellation
import os
import time


def Delock():

    Constellation.WriteInfo("System Unlock")
    os.system('/home/pi/Documents/test-serial-rpi.py')


@Constellation.MessageCallback()
def question(token):
    if token== 00000000-0000-0000-0000-000000000000:
        Constellation.WriteInfo("token null")
    else:
        Constellation.WriteInfo(token)
        Constellation.WriteInfo("Reception of the first token")
        fichier = open('data.txt',"w")
        fichier.write(token)
        fichier.close()
        return token

@Constellation.MessageCallback()
def reponse(token):
    Constellation.WriteInfo("Reception of the second token")
    a=token[36:45]
    bla=token[0:36]
    if a=='fr42kilj8':
        Constellation.WriteInfo("Key is valid")
        fichier = open('data.txt',"r")
        truc = fichier.readlines()
        fichier.close()
        fichier = open('data.txt',"w")
        fichier.write(bla)
        fichier.close()
        fichier = open('data.txt',"r")
        machin = fichier.readlines()
        fichier.close()
        if truc == machin:
            fichier = open('data.txt',"w")
            Delock()
        else:
            Constellation.WriteInfo("invalid token")

    else:
        Constellation.WriteInfo("invalid key")



@Constellation.MessageCallback()
def reset():
    Constellation.WriteInfo("raspberry's data reset")
    fichier = open('data.txt',"w")
    fichier.close()




Constellation.Start();