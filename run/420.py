import pymodbus
from pymodbus.pdu import ModbusRequest
from pymodbus.client.sync import ModbusSerialClient as ModbusClient
from pymodbus.transaction import ModbusRtuFramer
import requests
import json
import time
import random
import serial
from time import sleep
import minimalmodbus


# port = '/dev/ttyD420'
port = 'COM5'
baudrate = 9600
client = ModbusClient(
    method='rtu', port=port, baudrate=baudrate, parity='N', timeout=1
)
# client = serial.Serial(port, baudrate, timeout=1)
url = "http://localhost/trucems/public/api/"
get_payload = {}
headers = {
    'Content-Type': 'application/x-www-form-urlencoded'
}
connection = client.connect()

while True:
    # connection = True
    connection = client.connect()

    if(connection == True):
        try:
            response_plc = requests.request(
                "GET", url + "plc", headers=headers)

            json_plc = json.loads(response_plc.text)
            alarm = json_plc["data"]["alarm"]

            if(alarm == 0):
                result = client.write_coils(0, [1], unit=1)
            else:
                result = client.write_coils(0, [0], unit=1)

            response_configuration = requests.request(
                "GET", url + "sensor-value-logs", headers=headers, data=get_payload)
            json_get = json.loads(response_configuration.text)
            value = float(json_get["data"][0]["value"])
            if(value < 0):
                value = 0
            else:
                value = value
            fix_value = int(((0.008 * value) + 4) * 1000) + \
                random.randint(10000, 16000)
            if(fix_value < 20000):
                # digital to analog 4~20
                write = client.write_register(0, fix_value, unit=2)
        except Exception as e:
            do_nothing = ''
    else:
        do_nothing = ''
    time.sleep(1)