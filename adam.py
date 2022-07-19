from __future__ import print_function
import sys
from datetime import datetime
import psycopg2
import pymodbus
from pymodbus.pdu import ModbusRequest
from pymodbus.client.sync import ModbusSerialClient as ModbusClient
from pymodbus.transaction import ModbusRtuFramer
import requests
import json
import time
import random

try:
    # connect into database
    # mydb = psycopg2.connect(
    #     host="localhost", database="trucems_lumen_db", user="root", password="", )
    # mycursor = mydb.cursor()
    # port = '/dev/ttyADAM'
    port = 'COM10'
    baudrate = 9600
    client = ModbusClient(
        method='rtu', port=port, baudrate=baudrate, parity='N', timeout=1
    )
    print("sidik")
    while True:
        connection = client.connect()
        print(connection)
        # try to connect to adam
        if(connection == True):
            try:
                read = client.read_holding_registers(0, 8, unit=1)
                # print(read.registers)
                value1 = read.registers[0]  # reading register 30223
                # value2 = read.registers[1]  # reading register 30223
                # value3 = read.registers[2]  # reading register 30223
                fix_value1 = float(value1 * 20 / 4095)
                # fix_value2 = float(value2 * 20 / 4095)
                # print(round(1.5625 * fix_value1 - 6.25, 2))
                print(round(((6.25 * fix_value1) - 25), 3))
                # fix_value3 = round(float(value3 * 20 / 4095), 3)
                # print(fix_value1)
                # getdata = ("SELECT * FROM sensors WHERE is_deleted = '0'")
                # mycursor.execute(getdata)
                # data = mycursor.fetchall()
                # print(data)
                # for values in data:
                #     print(fix_value1)
                #     print(fix_value2)
                #     print(fix_value3)
                #     # print(print(value.registers))
                #     # update sensor_value_logs
                #     if(values[3] == 1):
                #         fix_value = fix_value1
                #     elif(values[3] == 2):
                #         fix_value = fix_value2
                #     else:
                #         fix_value = fix_value3
                #     print(values[3])
                #     print(fix_value)
                #     sql_update_log = (
                #         "UPDATE sensor_value_logs SET data = '" + str(fix_value) + "', voltage = '" + str(fix_value) + "' WHERE instrument_param_id = '"+str(values[3])+"'")
                #     mycursor.execute(sql_update_log)
                #     mydb.commit()
                #     print('update sensor logs')
            except Exception as e:
                do_nothing = ''
        else:
            do_nothing = ''
        time.sleep(1)
except Exception as e:
    print("[X] Database not connected " + e)
