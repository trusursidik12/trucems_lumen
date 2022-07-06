import pymodbus
from pymodbus.pdu import ModbusRequest
from pymodbus.client.sync import ModbusSerialClient as ModbusClient
from pymodbus.transaction import ModbusRtuFramer
import requests
import json
import time
import random


# port = '/dev/ttyD420'
port = 'COM9'
baudrate = 9600
client = ModbusClient(
    method='rtu', port=port, baudrate=baudrate, parity='N', timeout=1
)
url = "http://localhost/trucems/public/api/"
get_payload = {}
headers = {
    'Content-Type': 'application/x-www-form-urlencoded'
}

while True:
    connection = client.connect()
    print(connection)

    if(connection == True):
        try:
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
            print(fix_value)
            if(fix_value < 20000):
                # digital to analog 4~20
                write = client.write_register(0, fix_value, unit=1)
                write2 = client.write_register(1, (fix_value - 5000), unit=1)
                write3 = client.write_register(2, (fix_value - 10000), unit=1)
                print(write)
                print(write2)
                print(write3)
        except Exception as e:
            do_nothing = ''
    else:
        do_nothing = ''
    time.sleep(1)
