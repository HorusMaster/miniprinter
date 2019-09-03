#!/usr/bin/env python
# coding: utf-8

# In[1]:


import subprocess

def printTicked():
    result = subprocess.run(
        ['php', 'example/barcodeV2.php'],    # program and arguments
        stdout=subprocess.PIPE,  # capture stdout
       check=True               # raise exception if program fails
    )
    print(result.stdout)      


# In[1]:


import OPi.GPIO as GPIO
from time import sleep     
import MySQLdb
from datetime import date, datetime

DB_HOST = 'localhost'
DB_USER = 'print'
DB_PASS = 'root'
DB_NAME = 'printer'

def run_query(query=''):
    datos = [DB_HOST, DB_USER, DB_PASS, DB_NAME]

    conn = MySQLdb.connect(*datos) # Conectar a la base de datos
    cursor = conn.cursor()         # Crear un cursor
    cursor.execute(query)          # Ejecutar una consulta

    if query.upper().startswith('SELECT'):
        data = cursor.fetchall()   # Traer los resultados de un select
    else:
        conn.commit()              # Hacer efectiva la escritura de datos
        data = None

    cursor.close()                 # Cerrar el cursor
    conn.close()                   # Cerrar la conexion

    return data


GPIO.setboard(GPIO.PLUS2E)
GPIO.setmode(GPIO.BOARD)
GPIO.setup(11, GPIO.OUT)  
GPIO.setup(15, GPIO.IN, pull_up_down=GPIO.PUD_OFF)  

try:
    while True:                 # this will carry on until you hit CTRL+C
        if GPIO.input(15):      # if pin 15 == 1
            print ("Port 15 is 1/HIGH/True - LED ON")
            GPIO.output(11, 1)  # set port/pin value to 1/HIGH/True
            printTicked()
            # Esta es la consulta, insercion o actualizacion que vamos a lanzar
            date = datetime.now()
            query = "INSERT INTO comidas (fecha) VALUES ('%s')" % date
            run_query(query)
            sleep(5)
        else:
            #print ("Port 15 is 0/LOW/False - LED OFF")
            GPIO.output(11, 0)  # set port/pin value to 0/LOW/False
        sleep(0.1)              # wait 0.1 seconds

finally:                        # this block will run no matter how the try block exits
    print("Finally")
    GPIO.output(11, 0)
    GPIO.cleanup()              # clean up after yourself
# In[ ]:




