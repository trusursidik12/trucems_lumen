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
import socket

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
        # print(response_configuration.text)
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
                print(data_value)
                if(json_get_configuration["data"]["loop_count"] == 0):
                    patch_payload_configuration = 'is_calibration=0&calibration_type=0&is_calibration_history=0'
                    response = requests.request(
                        "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    print(response.text)
                    patch_payload_truncate = {}
                    response = requests.request(
                        "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
                else:
                    loop_count = json_get_configuration["data"]["loop_count"] - 1
                    patch_payload_configuration = 'is_calibration='+str(json_get_configuration["data"]["is_calibration_history"])+'&calibration_type=' + \
                        str(json_get_configuration["data"]
                            ["calibration_type"])+'&loop_count='+str(loop_count)
                    response = requests.request(
                        "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    print(response.text)
                    patch_payload_truncate = {}
                    response = requests.request(
                        "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)

            # is span calibration
            if(json_get_configuration["data"]["is_calibration"] == 3 and json_get_configuration["data"]["calibration_type"] == 2):
                if(json_get_configuration["data"]["loop_count"] == 0):
                    patch_payload_configuration = 'is_calibration=0&calibration_type=0&is_calibration_history=0'
                    response = requests.request(
                        "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    print(response.text)
                    patch_payload_truncate = {}
                    response = requests.request(
                        "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
                else:
                    loop_count = json_get_configuration["data"]["loop_count"] + 1
                    patch_payload_configuration = 'is_calibration='+str(json_get_configuration["data"]["is_calibration_history"])+'&calibration_type=' + \
                        str(json_get_configuration["data"]
                            ["calibration_type"])+'&loop_count='+str(loop_count)
                    response = requests.request(
                        "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    print(response.text)
                    patch_payload_truncate = {}
                    response = requests.request(
                        "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
        else:
            print(json_get_configuration["message"])
        time.sleep(0.5)
        # ser.close()  # Close serial port
except Exception as e:
    print("[X]  Not connected " + e)
