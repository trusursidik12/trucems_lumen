from __future__ import print_function
import sys
from ast import Str
import time
from datetime import datetime
from time import sleep
import serial  # Import module
import requests
import json
import struct
import logging

logf = open("error.log", "w")
# logf.write("Error : vvvv")
# logf.close()
try:
    # ser.open()
    # patch / update data sensor values
    patch_url_sensor_values = "http://localhost/trucems/public/api/sensor-value/1"
    # post data into calibration_logs
    post_url_calibration_logs = "http://localhost/trucems/public/api/calibration-logs"
    # get data into calibration_logs
    get_url_calibration_logs = "http://localhost/trucems/public/api/calibration-logs/get-last"
    # get configuration
    get_url_configuration = "http://localhost/trucems/public/api/configurations"
    # patch / update configuration
    patch_url_configuration = "http://localhost/trucems/public/api/configurations"
    # patch / update alarm
    patch_url_alarm = "http://localhost/trucems/public/api/alarm/update"
    # delete configuration
    delete_url_configuration = "http://localhost/trucems/public/api/calibration-logs"
    # payload
    get_payload = {}

    # headers
    headers = {
        'Content-Type': 'application/x-www-form-urlencoded'
    }
    # port on linux
    portx = "/dev/ttyWITEC"
    # port on windows
    # portx = "COM8"
    bps = 115200
    # time-out,None: Always wait for the operation, 0 to return the request result immediately, and the other values are waiting time-out.(In seconds)
    timex = 1
    # loop

    def float_to_hex(f):
        return hex(struct.unpack('<I', struct.pack('<f', f))[0])

    while True:
        logf = open("error.log", "w")
        try:
            witec_ser = serial.Serial(portx, bps, timeout=timex)
            # print("serial is connected!")
            now = datetime.now()
            timestamp = now.strftime("%Y-%m-%d %H:%M:%S")

            response_configuration = requests.request(
                "GET", get_url_configuration, headers=headers, data=get_payload)
            json_get_configuration = json.loads(response_configuration.text)
            if(json_get_configuration["success"] == True):
                # alarm
                msg_alarm = bytes.fromhex("50 00 00 00 00 00 55 00")
                result = witec_ser.write(msg_alarm)
                # print(result)
                data = str(witec_ser.readlines(1))
                data_value_alarm = data.replace("[b'", "").replace(
                    "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")
                if(data_value_alarm):
                    round_value_alarm = round(float(data_value_alarm), 3)
                else:
                    round_value_alarm = 0
                patch_payload_alarm = 'alarm='+str(round_value_alarm)+''
                response = requests.request(
                    "PATCH", patch_url_alarm, headers=headers, data=patch_payload_alarm)

                # read concentration
                msg = bytes.fromhex("0F 00 00 00 00 00 55 00")
                result = witec_ser.write(msg)
                # print(result)
                data = str(witec_ser.readlines(1))
                # print(data)
                data_value = data.replace("[b'", "").replace(
                    "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")
                if(data_value):
                    if(json_get_configuration["data"]["is_calibration"] == 1):
                        round_value = round(float(data_value), 2)
                    else:
                        data_value = data_value if float(
                            data_value) >= 0 else 0
                        round_value = round(
                            float(data_value), 2)
                else:
                    # value set when the sensor disconnected!
                    round_value = -2.222
                # update sensor values
                patch_payload_sensor_values = 'value='+str(round_value)+''
                response = requests.request(
                    "PATCH", patch_url_sensor_values, headers=headers, data=patch_payload_sensor_values)

                # is zero calibration
                if(json_get_configuration["data"]["is_calibration"] == 1 and json_get_configuration["data"]["calibration_type"] == 1 and json_get_configuration["data"]["target_value"] != ''):
                    # print(json_get_configuration)

                    msg = bytes.fromhex("11 00 00 00 00 00 7A 00")
                    result = witec_ser.write(msg)
                    data = str(witec_ser.readlines(1))

                    patch_payload_configuration = 'target_value=""'
                    response = requests.request(
                        "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)

                # is span calibration
                if(json_get_configuration["data"]["is_calibration"] == 1 and json_get_configuration["data"]["calibration_type"] == 2 and json_get_configuration["data"]["target_value"] != ''):

                    response_calibration_logs = requests.request(
                        "GET", get_url_calibration_logs, headers=headers, data=get_payload)
                    json_get_calibation_logs = json.loads(
                        response_calibration_logs.text)

                    n = float_to_hex(
                        json_get_configuration["data"]["target_value"])[2:]
                    m = str(n)

                    # reverse
                    def little(string):
                        t = bytearray.fromhex(string)
                        t.reverse()
                        return ''.join(format(x, '02x') for x in t).upper()

                    k = little(m)
                    # print(k)

                    value1 = k[0:2]
                    value2 = k[2:4]
                    value3 = k[4:6]
                    value4 = k[6:8]

                    setSpan = "60 02 " + \
                        str(value1)+" "+str(value2) + " " + \
                        str(value3)+" "+str(value4)+" 7A 00"
                    msg = bytes.fromhex(setSpan)
                    result = witec_ser.write(msg)
                    data = str(witec_ser.readlines(1))
                    # print(response_delete.text)
            # else:
                # print(json_get_configuration)
            time.sleep(1)
            witec_ser.close()  # Close serial port
        except serial.serialutil.SerialException as e:
            now = datetime.now()
            timestamp = now.strftime("%Y-%m-%d %H:%M:%S")
            # print(e)
            response_configuration = requests.request(
                "GET", get_url_configuration, headers=headers, data=get_payload)
            json_get_configuration = json.loads(response_configuration.text)

            if(json_get_configuration["success"] == True):
                # value set when the USB Port disconnected!
                round_value = -1.111
                # print(round_value)
                patch_payload_sensor_values = 'value='+str(round_value)+''
                response = requests.request(
                    "PATCH", patch_url_sensor_values, headers=headers, data=patch_payload_sensor_values)
                # print(json.loads(response.text))
            # else:
                # print(json_get_configuration)
            logf.write("Error "+timestamp+" : \n"+str(e))
            logf.close()
            time.sleep(5)
except Exception as e:
    now = datetime.now()
    timestamp = now.strftime("%Y-%m-%d %H:%M:%S")
    # print("[X]  Not connected ", e)
    logf.write("Error "+timestamp+" : \n".format(str(e)))
    logf.close()
