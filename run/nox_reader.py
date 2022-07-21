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
import serial

try:
    # connect into database
    mydb = psycopg2.connect(
        host="localhost", database="trudas_db", user="postgres", password="root", )
    mycursor = mydb.cursor()

    # port = '/dev/ttyADAM'
    port = 'COM10'
    baudrate = 9600
    client = ModbusClient(
        method='rtu', port=port, baudrate=baudrate, parity='N', timeout=1
    )
    # print("sidik")
    while True:
        connection = True
        # connection = client.connect()
        # print(connection)
        # try to connect to adam
        # date now
        now = datetime.now()
        day_of_number = now.strftime("%d-%H:%M:%S")
        day_time = now.strftime("%Y%m%d%M")
        timestamp = now.strftime("%Y-%m-%d %H:%M:%S")

        if(connection == True):
            try:
                # witec_ser = serial.Serial(port, baudrate, timeout=1)

                # read = client.read_holding_registers(0, 8, unit=1)
                # reverse value to origin voltage
                getdata = (
                    "SELECT * FROM sensors WHERE is_deleted = '0' ORDER BY id ASC")
                mycursor.execute(getdata)
                data = mycursor.fetchall()
                print(data)
                exit()
                for ain in data:
                    # get value from reader
                    # value = read.registers[ain[2]]  # reading register 30223
                    # reverse value to origin voltage
                    # voltage_value = float(value * 20 / 4095)
                    # print(voltage_value)
                    # if(ain[2] == 0){
                    #     read =
                    # }
                    msg = bytes.fromhex("0F 00 00 00 00 00 55 00")
                    result = witec_ser.write(msg)
                    data = str(witec_ser.readlines(1))
                    data_value = data.replace("[b'", "").replace(
                        "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")
                    round_value = round(float(data_value), 2)
                    log_check = (
                        "SELECT COUNT(*) FROM sensor_value_logs WHERE instrument_param_id = '" + str(ain[3]) + "'")
                    mycursor.execute(log_check)
                    mydb.commit()
                    reader_value = mycursor.fetchone()[0]
                    if(reader_value != 0):
                        # update sensor_value_logs
                        sql_update_log = (
                            "UPDATE sensor_value_logs SET data = '" + str(eval(ain[6])) + "', voltage = '" + str(voltage_value) + "' WHERE instrument_param_id = '" + str(ain[3]) + "'")
                        mycursor.execute(sql_update_log)
                        mydb.commit()
                        print('update sensor logs')
                    else:
                        # insert sensor_value_logs
                        sql_insert_log = (
                            "INSERT INTO sensor_value_logs (instrument_param_id, data, voltage, xtimestamp) VALUES ('" + str(ain[3]) + "','" + str(eval(ain[6])) + "','" + str(voltage_value) + "','" + timestamp + "')")
                        mycursor.execute(sql_insert_log)
                        mydb.commit()
                        print('insert sensor logs')
            except Exception as e:
                do_nothing = ''
        else:
            do_nothing = ''
        time.sleep(1)
except Exception as e:
    print("[X] Database not connected " + e)
