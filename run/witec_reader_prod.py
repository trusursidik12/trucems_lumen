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
                    round_value = round(float(data_value), 3)
                else:
                    # value set when the sensor disconnected!
                    round_value = -2.222
                # print(round_value)
                # exit()
                # update sensor values
                patch_payload_sensor_values = 'value='+str(round_value)+''
                response = requests.request(
                    "PATCH", patch_url_sensor_values, headers=headers, data=patch_payload_sensor_values)
                # print(json.loads(response.text))

                # if(json_get_configuration["data"]["is_calibration"] == 1 or json_get_configuration["data"]["is_calibration"] == 2):
                #     post_payload_calibration_logs = 'value=' + \
                #         str(round_value)+''
                #     response = requests.request(
                #         "POST", post_url_calibration_logs, headers=headers, data=post_payload_calibration_logs)
                # print(response.text)

                # is zero calibration
                if(json_get_configuration["data"]["is_calibration"] == 1 and json_get_configuration["data"]["calibration_type"] == 1 and json_get_configuration["data"]["target_value"] != ''):
                    print(json_get_configuration)
                    # msg = bytes.fromhex("08 00 00 00 00 00 55 00")
                    # msg = bytes.fromhex("11 00 00 00 00 00 55 00")
                    # result = witec_ser.write(msg)
                    # data = str(witec_ser.readlines(1))
                    # data_value = data.replace("[b'", "").replace(
                    #     "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")
                    # print("ZERO")

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

                    setZero = "08 00 " + \
                        str(value1)+" "+str(value2) + " " + \
                        str(value3)+" "+str(value4)+" 55 00"
                    # setZero = "11 00 " + \
                    #     str(value1)+" "+str(value2) + " " + \
                    #     str(value3)+" "+str(value4)+" 55 00"
                    # zero = "08 00 00 00 00 00 55 00"
                    msg = bytes.fromhex(setZero)
                    result = witec_ser.write(msg)
                    data = str(witec_ser.readlines(1))

                    patch_payload_configuration = 'target_value=""'
                    response = requests.request(
                        "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)

                    # if(json_get_configuration["data"]["loop_count"] != 0):
                    #     loop_count = json_get_configuration["data"]["loop_count"] - 1
                    #     patch_payload_configuration = 'is_calibration='+str(json_get_configuration["data"]["is_calibration_history"])+'&calibration_type=' + \
                    #         str(json_get_configuration["data"]
                    #             ["calibration_type"])+'&loop_count='+str(loop_count)
                    #     response = requests.request(
                    #         "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    #     # print(response.text)
                    #     patch_payload_truncate = {}
                    #     response_delete = requests.request(
                    #         "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
                    #     # print(response_delete.text)
                    # else:
                    #     patch_payload_configuration = 'is_calibration=0&calibration_type=0&is_calibration_history=0&loop_count=0'
                    #     response = requests.request(
                    #         "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    #     # print(response.text)
                    #     patch_payload_truncate = {}
                    #     response_delete = requests.request(
                    #         "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
                    # print(response_delete.text)

                # is span calibration
                if(json_get_configuration["data"]["is_calibration"] == 1 and json_get_configuration["data"]["calibration_type"] == 2 and json_get_configuration["data"]["target_value"] != ''):

                    response_calibration_logs = requests.request(
                        "GET", get_url_calibration_logs, headers=headers, data=get_payload)
                    json_get_calibation_logs = json.loads(
                        response_calibration_logs.text)
                    # print(json_get_calibation_logs["data"]["value"])

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

                    # setZero = "60 02 " + \
                    #     str(value1)+" "+str(value2) + " " + \
                    #     str(value3)+" "+str(value4)+" 55 00"
                    # msg = bytes.fromhex(zero)
                    # result = witec_ser.write(msg)
                    # data = str(witec_ser.readlines(1))

                    # patch_payload_configuration = 'target_value=""'
                    # response = requests.request(
                    #     "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)

                    # def float_to_hex(f):
                    #     return hex(struct.unpack('<I', struct.pack('<f', f))[0])

                    # n = float_to_hex(
                    #     json_get_calibation_logs["data"]["value"])[2:]
                    # m = str(n)
                    # # print(m)

                    # # reverse
                    # def little(string):
                    #     t = bytearray.fromhex(string)
                    #     t.reverse()
                    #     return ''.join(format(x, '02x') for x in t).upper()

                    # k = little(m)
                    # # print(k)

                    # value1 = k[0:2]
                    # value2 = k[2:4]
                    # value3 = k[4:6]
                    # value4 = k[6:8]
                    # print(value1)
                    # print(value2)
                    # print(value3)
                    # print(value4)

                    # msg = bytes.fromhex("08 00 00 00 00 00 55 00")
                    # result = witec_ser.write(msg)
                    # data = str(witec_ser.readlines(1))
                    # data_value = data.replace("[b'", "").replace(
                    #     "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")

                    # print("|||||||||||||||||+++++++SPAN++++++++++||||||||||")
                    # print("60 02 "+str(value1)+" "+str(value2) +
                    # " "+str(value3)+" "+str(value4)+" 55 00")
                    # print("|||||||||||||||||+++++++SPAN++++++++++||||||||||")

                    # if(json_get_configuration["data"]["loop_count"] != 0):
                    #     loop_count = json_get_configuration["data"]["loop_count"] - 1
                    #     patch_payload_configuration = 'is_calibration='+str(json_get_configuration["data"]["is_calibration_history"])+'&calibration_type=' + \
                    #         str(json_get_configuration["data"]
                    #             ["calibration_type"])+'&loop_count='+str(loop_count)
                    #     response = requests.request(
                    #         "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    #     # print(response.text)
                    #     patch_payload_truncate = {}
                    #     response_delete = requests.request(
                    #         "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
                    #     # print(response_delete.text)
                    # else:
                    #     patch_payload_configuration = 'is_calibration=0&calibration_type=0&is_calibration_history=0&loop_count=0'
                    #     response = requests.request(
                    #         "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    #     # print(response.text)
                    #     patch_payload_truncate = {}
                    #     response_delete = requests.request(
                    #         "DELETE", delete_url_configuration, headers=headers, data=patch_payload_truncate)
                    # print(response_delete.text)
            # else:
                # print(json_get_configuration)
            time.sleep(1)
            witec_ser.close()  # Close serial port
        except serial.serialutil.SerialException as e:
            now = datetime.now()
            timestamp = now.strftime("%Y-%m-%d %H:%M:%S")
            print(e)
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
    print("[X]  Not connected ", e)
    logf.write("Error "+timestamp+" : \n".format(str(e)))
    logf.close()
