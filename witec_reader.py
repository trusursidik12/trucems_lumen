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


def isRelayOpen(plc_ser, is_relay_open):
    if is_relay_open == 1:
        hex = "01 0F 00 00 00 08 01 02 7F 54"
        relay = "3"
    elif is_relay_open == 2:
        hex = "01 0F 00 00 00 08 01 04 FF 56"
        relay = "3"
    elif is_relay_open == 4:
        hex = "01 0F 00 00 00 08 01 08 FF 53"
        relay = "3"
    elif is_relay_open == 0:
        hex = "01 0F 00 00 00 08 01 01 3F 55"
        relay = "0"
    else:
        hex = None
        relay = "0"
    if(hex != None):
        try:
            plc_msg = bytes.fromhex(hex)
            plc_open = plc_ser.write(plc_msg)
            plc_open_read = len(plc_ser.read(size=10))
            if((plc_open_read > 0) and (is_relay_open == 1 or is_relay_open == 2)):
                response = requests.request(
                    "PATCH", url + "relay", headers=headers, data="is_relay_open="+relay)
            elif plc_open_read == 0:
                relay = "0"
                response = requests.request(
                    "PATCH", url + "relay", headers=headers, data="is_relay_open="+relay)
        except serial.SerialTimeoutException as e:
            return "0"
    return relay


try:
    # ser.open()

    # url
    url = "http://localhost/trucems/public/api/"
    # payload
    get_payload = {}

    # headers
    headers = {
        'Content-Type': 'application/x-www-form-urlencoded'
    }
    # port on linux
    # witec_port = "/dev/ttyWITEC"
    # plc_port = "/dev/ttyPLC"
    # witec_port = "/dev/ttyUSB0"
    # plc_port = "/dev/ttyUSB1"
    # port on windows
    witec_port = "COM8"
    witec_bps = 115200
    plc_port = "COM5"
    plc_bps = 9600
    # time-out,None: Always wait for the operation, 0 to return the request result immediately, and the other values are waiting time-out.(In seconds)
    timex = 1
    # open sampling
    patch_payload_relay = 'is_calibration=0&calibration_type=0&is_relay_open=0&is_calibration_history=0&loop_count=0'
    response = requests.request(
        "PATCH", url + "relay", headers=headers, data=patch_payload_relay)
    # print(response.text)

    # loop
    while True:
        logf = open("error.log", "w")
        try:
            witec_ser = serial.Serial(witec_port, witec_bps, timeout=timex)
            plc_ser = serial.Serial(plc_port, plc_bps, timeout=timex)
            now = datetime.now()
            timestamp = now.strftime("%Y-%m-%d %H:%M:%S")

            response_configuration = requests.request(
                "GET", url + "configurations", headers=headers, data=get_payload)
            json_get_configuration = json.loads(response_configuration.text)

            if(json_get_configuration["success"] == True):
                # read data from sensors
                sampling_msg = bytes.fromhex("0F 00 00 00 00 00 55 00")
                result = witec_ser.write(sampling_msg)
                data = str(witec_ser.readlines(1))
                data_value = data.replace("[b'", "").replace(
                    "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")
                if(data_value):
                    round_value = round(float(data_value), 3)
                else:
                    # value set when the sensor disconnected!
                    round_value = -2.222
                # print(round_value)
                # update sensor values
                patch_payload_sensor_values = 'value='+str(round_value)+''
                response = requests.request(
                    "PATCH", url + "sensor-value/1", headers=headers, data=patch_payload_sensor_values)
                # print(json.loads(response.text))
                isRelayOpen(
                    plc_ser, json_get_configuration["data"]["is_relay_open"])

                if(json_get_configuration["data"]["is_calibration"] == 1 or json_get_configuration["data"]["is_calibration"] == 2):
                    post_payload_calibration_logs = 'value=' + \
                        str(round_value)+''
                    response = requests.request(
                        "POST", url + "calibration-logs", headers=headers, data=post_payload_calibration_logs)
                    # print(response.text)

                # is zero calibration
                if(json_get_configuration["data"]["is_calibration"] == 3 and json_get_configuration["data"]["calibration_type"] == 1):
                    zero_msg = bytes.fromhex("08 00 00 00 00 00 55 00")
                    result = witec_ser.write(zero_msg)
                    data = str(witec_ser.readlines(1))
                    data_value = data.replace("[b'", "").replace(
                        "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")
                    # print("ZERO")

                    if(json_get_configuration["data"]["loop_count"] != 0):
                        loop_count = json_get_configuration["data"]["loop_count"] - 1
                        patch_payload_configuration = 'is_calibration='+str(json_get_configuration["data"]["is_calibration_history"])+'&calibration_type=' + \
                            str(json_get_configuration["data"]
                                ["calibration_type"])+'&loop_count='+str(loop_count)
                        response = requests.request(
                            "PATCH", url + "configurations", headers=headers, data=patch_payload_configuration)
                        # print(response.text)
                        patch_payload_truncate = {}
                        response_delete = requests.request(
                            "DELETE", url + "calibration-logs", headers=headers, data=patch_payload_truncate)
                        # print(response_delete.text)
                    else:
                        patch_payload_configuration = 'is_calibration=0&calibration_type=0&is_relay_open=0&is_calibration_history=0&loop_count=0'
                        response = requests.request(
                            "PATCH", url + "configurations", headers=headers, data=patch_payload_configuration)
                        # print(response.text)
                        patch_payload_truncate = {}
                        response_delete = requests.request(
                            "DELETE", url + "calibration-logs", headers=headers, data=patch_payload_truncate)
                        # print(response_delete.text)

                # is span calibration
                if(json_get_configuration["data"]["is_calibration"] == 3 and json_get_configuration["data"]["calibration_type"] == 2):
                    response_calibration_logs = requests.request(
                        "GET", url + "calibration-logs/get-last", headers=headers, data=get_payload)
                    json_get_calibation_logs = json.loads(
                        response_calibration_logs.text)
                    # print(json_get_calibation_logs["data"]["value"])

                    if(json_get_calibation_logs["data"] is not None):
                        def float_to_hex(f):
                            return hex(struct.unpack('<I', struct.pack('<f', f))[0])

                        n = float_to_hex(
                            json_get_calibation_logs["data"]["value"])[2:]
                        m = str(n)
                        # print(m)

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
                        # print(value1)
                        # print(value2)
                        # print(value3)
                        # print(value4)

                        # print("60 00 "+str(value1)+" "+str(value2) +
                        #       " "+str(value3)+" "+str(value4)+" 55 00")
                        # span_msg = bytes.fromhex("60 00 "+str(value1)+" "+str(value2) +
                        #                          " "+str(value3)+" "+str(value4)+" 55 00")
                        # result = witec_ser.write(span_msg)
                        # data = str(witec_ser.readlines(1))
                        # data_value = data.replace("[b'", "").replace(
                        #     "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")

                    if(json_get_configuration["data"]["loop_count"] != 0):
                        loop_count = json_get_configuration["data"]["loop_count"] - 1
                        patch_payload_configuration = 'is_calibration='+str(json_get_configuration["data"]["is_calibration_history"])+'&calibration_type=' + \
                            str(json_get_configuration["data"]
                                ["calibration_type"])+'&loop_count='+str(loop_count)
                        response = requests.request(
                            "PATCH", url + "configurations", headers=headers, data=patch_payload_configuration)
                        # print(response.text)
                        patch_payload_truncate = {}
                        response_delete = requests.request(
                            "DELETE", url + "calibration-logs", headers=headers, data=patch_payload_truncate)
                        # print(response_delete.text)
                    else:
                        patch_payload_configuration = 'is_calibration=0&calibration_type=0&is_relay_open=0&is_calibration_history=0&loop_count=0'
                        response = requests.request(
                            "PATCH", url + "configurations", headers=headers, data=patch_payload_configuration)
                        # print(response.text)
                        patch_payload_truncate = {}
                        response_delete = requests.request(
                            "DELETE", url + "calibration-logs", headers=headers, data=patch_payload_truncate)
                        # print(response_delete.text)
            else:
                do_nothing = ''
            time.sleep(1)
            witec_ser.close()  # Close serial port
            plc_ser.close()  # Close serial port
        except serial.serialutil.SerialException as e:
            now = datetime.now()
            timestamp = now.strftime("%Y-%m-%d %H:%M:%S")
            print("serial not connected!")
            response_configuration = requests.request(
                "GET", url + "configurations", headers=headers, data=get_payload)
            json_get_configuration = json.loads(response_configuration.text)

            if(json_get_configuration["success"] == True):
                # value set when the USB Port disconnected!
                round_value = -1.111
                # print(round_value)
                patch_payload_sensor_values = 'value='+str(round_value)+''
                response = requests.request(
                    "PATCH", url + "sensor-value/1", headers=headers, data=patch_payload_sensor_values)
                # print(json.loads(response.text))
            else:
                do_nothing = ''
            logf.write("Error "+timestamp+" : \n"+str(e))
            logf.close()
            time.sleep(5)
except Exception as e:
    now = datetime.now()
    timestamp = now.strftime("%Y-%m-%d %H:%M:%S")
    print("[X]  Not connected ", e)
    logf.write("Error "+timestamp+" : \n".format(str(e)))
    logf.close()
