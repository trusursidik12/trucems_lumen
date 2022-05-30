from __future__ import print_function
import sys
# from labjack import ljm
from ast import Str
import time
from datetime import datetime
# import psycopg2
from time import sleep
import serial  # Import module
import requests
import json
import struct

try:
    portx = "COM7"
    bps = 115200
    # time-out,None: Always wait for the operation, 0 to return the request result immediately, and the other values are waiting time-out.(In seconds)
    timex = 1
    # if(serial.Serial(portx, bps, timeout=timex) == True):
    #     print("oke")
    # else:
    #     print("tidak oke")
    # try:
    ser = serial.Serial(portx, bps, timeout=timex)
    # except Exception as e:
    #     print("[X]  Error " + e)

    # patch / update data sensor values
    patch_url_sensor_values = "http://localhost/trucems_lumen/public/api/sensor-value/1"
    # post data into calibration_logs
    post_url_calibration_logs = "http://localhost/trucems_lumen/public/api/calibration-logs"
    # get data into calibration_logs
    get_url_calibration_logs = "http://localhost/trucems_lumen/public/api/calibration-logs/get-last"
    # get configuration
    get_url_configuration = "http://localhost/trucems_lumen/public/api/configurations"
    # patch / update configuration
    patch_url_configuration = "http://localhost/trucems_lumen/public/api/configurations"
    # delete configuration
    delete_url_configuration = "http://localhost/trucems_lumen/public/api/calibration-logs"
    # payload
    get_payload = {}

    # headers
    headers = {
        'Content-Type': 'application/x-www-form-urlencoded'
    }

    # loop
    while True:
        # setting date
        now = datetime.now()
        timestamp = now.strftime("%Y-%m-%d %H:%M:%S")

        response_configuration = requests.request(
            "GET", get_url_configuration, headers=headers, data=get_payload)
        json_get_configuration = json.loads(response_configuration.text)

        if(json_get_configuration["success"] == True):
            msg = bytes.fromhex("17 00 00 00 00 00 55 30")
            result = ser.write(msg)
            data = str(ser.readlines(1))
            data_value = data.replace("[b'", "").replace(
                "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")
            print(data_value)
            if(data_value):
                round_value = round(float(data_value), 3)
            else:
                round_value = 0
            # update data
            patch_payload_sensor_values = 'value='+str(round_value)+''
            response = requests.request(
                "PATCH", patch_url_sensor_values, headers=headers, data=patch_payload_sensor_values)
            print(json.loads(response.text))

            if(json_get_configuration["data"]["is_calibration"] == 1 or json_get_configuration["data"]["is_calibration"] == 2):
                post_payload_calibration_logs = 'value=' + \
                    str(round_value)+''
                response = requests.request(
                    "POST", post_url_calibration_logs, headers=headers, data=post_payload_calibration_logs)
                print(response.text)

            # is zero calibration
            if(json_get_configuration["data"]["is_calibration"] == 3 and json_get_configuration["data"]["calibration_type"] == 1):
                msg = bytes.fromhex("08 00 00 00 00 00 55 00")
                result = ser.write(msg)
                data = str(ser.readlines(1))
                data_value = data.replace("[b'", "").replace(
                    "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")
                print("|||||||||||||||||||++++++++ZERO++++++++||||||||||||||||||||||||")

                if(json_get_configuration["data"]["loop_count"] != 0):
                    loop_count = json_get_configuration["data"]["loop_count"] - 1
                    patch_payload_configuration = 'is_calibration='+str(json_get_configuration["data"]["is_calibration_history"])+'&calibration_type=' + \
                        str(json_get_configuration["data"]
                            ["calibration_type"])+'&loop_count='+str(loop_count)
                    response = requests.request(
                        "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    print(response.text)
                    patch_payload_truncate = {}
                    response_delete = requests.request(
                        "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
                    print(response_delete.text)
                else:
                    patch_payload_configuration = 'is_calibration=0&calibration_type=0&is_calibration_history=0&loop_count=0'
                    response = requests.request(
                        "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    print(response.text)
                    patch_payload_truncate = {}
                    response_delete = requests.request(
                        "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
                    print(response_delete.text)

            # is span calibration
            if(json_get_configuration["data"]["is_calibration"] == 3 and json_get_configuration["data"]["calibration_type"] == 2):

                response_calibration_logs = requests.request(
                    "GET", get_url_calibration_logs, headers=headers, data=get_payload)
                json_get_calibation_logs = json.loads(
                    response_calibration_logs.text)
                print(json_get_calibation_logs["data"]["value"])

                def float_to_hex(f):
                    return hex(struct.unpack('<I', struct.pack('<f', f))[0])

                n = float_to_hex(json_get_calibation_logs["data"]["value"])[2:]
                m = str(n)
                print(m)

                # reverse
                def little(string):
                    t = bytearray.fromhex(string)
                    t.reverse()
                    return ''.join(format(x, '02x') for x in t).upper()

                k = little(m)
                print(k)

                value1 = k[0:2]
                value2 = k[2:4]
                value3 = k[4:6]
                value4 = k[6:8]
                print(value1)
                print(value2)
                print(value3)
                print(value4)

                msg = bytes.fromhex("08 00 00 00 00 00 55 00")
                result = ser.write(msg)
                data = str(ser.readlines(1))
                data_value = data.replace("[b'", "").replace(
                    "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")

                print("|||||||||||||||||+++++++SPAN++++++++++||||||||||")
                print("60 00 "+str(value1)+" "+str(value2) +
                      " "+str(value3)+" "+str(value4)+" 55 00")
                print("|||||||||||||||||+++++++SPAN++++++++++||||||||||")

                if(json_get_configuration["data"]["loop_count"] != 0):
                    loop_count = json_get_configuration["data"]["loop_count"] - 1
                    patch_payload_configuration = 'is_calibration='+str(json_get_configuration["data"]["is_calibration_history"])+'&calibration_type=' + \
                        str(json_get_configuration["data"]
                            ["calibration_type"])+'&loop_count='+str(loop_count)
                    response = requests.request(
                        "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    print(response.text)
                    patch_payload_truncate = {}
                    response_delete = requests.request(
                        "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
                    print(response_delete.text)
                else:
                    patch_payload_configuration = 'is_calibration=0&calibration_type=0&is_calibration_history=0&loop_count=0'
                    response = requests.request(
                        "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    print(response.text)
                    patch_payload_truncate = {}
                    response_delete = requests.request(
                        "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
                    print(response_delete.text)
        else:
            print(json_get_configuration["message"])
        time.sleep(1)
        # ser.close()  # Close serial port
except Exception as e:
    print("[X]  Not connected " + e)