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
    link_url = "http://localhost/trucems/public/"
    # patch / update data sensor values
    patch_url_sensor_values = link_url + "api/sensor-value/"
    # post data into calibration_logs
    post_url_calibration_logs = link_url + "api/calibration-logs"
    # get data into calibration_logs
    get_url_calibration_logs = link_url + "api/calibration-logs/get-last"
    # get configuration
    get_url_configuration = link_url + "api/configurations"
    # patch / update configuration
    patch_url_configuration = link_url + "api/configurations"
    # patch / update alarm
    patch_url_alarm = link_url + "api/alarm/update"
    # delete configuration
    delete_url_configuration = link_url + "api/calibration-logs"
    # get sensors
    get_url_sensors = link_url + "api/sensor-lists"
    # get PLC
    get_url_plc_status = link_url + "api/plc"
    # payload
    get_payload = {}

    # headers
    headers = {
        'Content-Type': 'application/x-www-form-urlencoded'
    }
    # port on linux
    portx = "/dev/ttyWITEC"
    # port on windows
    # portx = "COM11"
    bps = 115200
    # time-out,None: Always wait for the operation, 0 to return the request result immediately, and the other values are waiting time-out.(In seconds)
    timex = 1
    # loop

    # float to hexadecimal
    def float_to_hex(f):
        return hex(struct.unpack('<I', struct.pack('<f', f))[0])

    # reverse front to end like '123' to '321'
    def little(string):
        t = bytearray.fromhex(string)
        t.reverse()
        return ''.join(format(x, '02x') for x in t).upper()

    while True:
        logf = open("error.log", "w")
        try:
            witec_ser = serial.Serial(portx, bps, timeout=timex)

            # set data now
            now = datetime.now()
            timestamp = now.strftime("%Y-%m-%d %H:%M:%S")

            # start get plc status
            response_cga_status = requests.request(
                "GET", get_url_plc_status, headers=headers, data=get_payload)
            json_get_cga_status = json.loads(
                response_cga_status.text)
            # end get plc status

            # get configurations
            response_configuration = requests.request(
                "GET", get_url_configuration, headers=headers, data=get_payload)
            json_get_configuration = json.loads(response_configuration.text)
            # check configurations response
            if(json_get_configuration["success"] == True):
                # start alarm
                msg_alarm = bytes.fromhex("50 00 00 00 00 00 55 00")
                result = witec_ser.write(msg_alarm)
                data_alarm = str(witec_ser.readlines(1))
                data_value_alarm = data_alarm.replace("[b'", "").replace(
                    "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")
                if(data_value_alarm):
                    round_value_alarm = round(float(data_value_alarm), 3)
                else:
                    round_value_alarm = 0
                patch_payload_alarm = 'alarm='+str(round_value_alarm)+''
                response = requests.request(
                    "PATCH", patch_url_alarm, headers=headers, data=patch_payload_alarm)
                # end alarm

                # start read concentration
                response_sensor_lists = requests.request(
                    "GET", get_url_sensors, headers=headers, data=get_payload)
                json_get_sensor = json.loads(response_sensor_lists.text)
                for ch in json_get_sensor:
                    if(ch['code'] != "nox"):
                        msg = bytes.fromhex(ch['read_formula'])
                        result = witec_ser.write(msg)
                        data_conc = str(witec_ser.readlines(1))
                        # start parse data
                        data_value = data_conc.replace("[b'", "").replace(
                            "\\r\\n']", "").replace("[]", "").replace("\\x00']", "")
                        # end parse data
                        # start check sensors is online
                        if(data_value):
                            # start get plc status
                            response_plc_status = requests.request(
                                "GET", get_url_plc_status, headers=headers, data=get_payload)
                            json_get_plc_status = json.loads(
                                response_plc_status.text)
                            # end get plc status

                            if(json_get_plc_status["data"]["is_calibration"] == 1):
                                round_value = round(float(data_value), 2)
                            else:
                                data_value = data_value if float(
                                    data_value) >= 0 else 0
                                round_value = round(
                                    float(data_value), 2)
                            # end calibration condition check
                        else:
                            # value set when the sensor is offline!
                            round_value = -2.222
                        # end check sensors is online
                        # update sensor values

                        patch_payload_sensor_values = 'value=' + \
                            str(round_value)+''
                        response = requests.request(
                            "PATCH", patch_url_sensor_values + str(ch['id']), headers=headers, data=patch_payload_sensor_values)

                        if(ch['code'] == "no"):
                            getNo2 = requests.request(
                                "GET", link_url+"api/sensor-value/3", headers=headers)
                            no2 = json.loads(getNo2.text)
                            round_value = round(no2['value'] + round_value, 2)
                            patch_payload_sensor_values = 'value=' + \
                                str(round_value)+''
                            response = requests.request(
                                "PATCH", patch_url_sensor_values + str(4), headers=headers, data=patch_payload_sensor_values)
                        if(ch['code'] == "no2"):
                            getNo = requests.request(
                                "GET", link_url+"api/sensor-value/1", headers=headers)
                            no = json.loads(getNo.text)
                            round_value = round(no['value'] + round_value, 2)
                            patch_payload_sensor_values = 'value=' + \
                                str(round_value)+''
                            response = requests.request(
                                "PATCH", patch_url_sensor_values + str(4), headers=headers, data=patch_payload_sensor_values)
                    # start calibration
                    # start is zero calibration
                    if(json_get_configuration["data"]["is_calibration"] == 1 and json_get_configuration["data"]["calibration_type"] == 1 and json_get_configuration["data"]["target_value"] != None):
                        msg = bytes.fromhex("11 00 00 00 00 00 7A 00")
                        result = witec_ser.write(msg)
                        data_zero = str(witec_ser.readlines(1))
                        patch_payload_configuration = 'target_value=-1'
                        response = requests.request(
                            "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    # end is zero calibration
                    # is span calibration
                    if(json_get_configuration["data"]["is_calibration"] == 1 and json_get_configuration["data"]["calibration_type"] == 2 and json_get_configuration["data"]["target_value"] != None):
                        # start check to select parameters to calibration
                        if(json_get_configuration["data"]["sensor_id"] == ch['id']):
                            n = float_to_hex(
                                json_get_configuration["data"]["target_value"])[2:]
                            m = str(n)

                            k = little(m)

                            # start parse
                            value1 = k[0:2]
                            value2 = k[2:4]
                            value3 = k[4:6]
                            value4 = k[6:8]
                            # end parse
                            data_formula = ch['write_formula']
                            formula = data_formula.replace("AA", str(value1)).replace(
                                "BB", str(value2)).replace("CC", str(value3)).replace("DD", str(value4))
                            msg = bytes.fromhex(formula)
                            result = witec_ser.write(msg)
                            data_span = str(witec_ser.readlines(1))

                            patch_payload_configuration = 'target_value=-1'
                            response = requests.request(
                                "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                        # start check to select parameters to calibration
                    # end is span calibration
                    # end calibration

                    # start calibration from CGA Mode
                    # start is zero calibration
                    if(json_get_cga_status['data']['is_cga'] == 1 and json_get_configuration["data"]["calibration_type"] == 1 and json_get_configuration["data"]["target_value"] != None):
                        msg = bytes.fromhex("11 00 00 00 00 00 7A 00")
                        result = witec_ser.write(msg)
                        data_zero = str(witec_ser.readlines(1))
                        patch_payload_configuration = 'target_value=-1'
                        response = requests.request(
                            "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                    # end is zero calibration
                    # is span calibration
                    if(json_get_cga_status['data']['is_cga'] == 1 and json_get_configuration["data"]["calibration_type"] == 2 and json_get_configuration["data"]["target_value"] != None):
                        # start check to select parameters to calibration
                        if(json_get_configuration["data"]["sensor_id"] == ch['id']):
                            n = float_to_hex(
                                json_get_configuration["data"]["target_value"])[2:]
                            m = str(n)

                            k = little(m)

                            # start parse
                            value1 = k[0:2]
                            value2 = k[2:4]
                            value3 = k[4:6]
                            value4 = k[6:8]
                            # end parse
                            data_formula = ch['write_formula']
                            formula = data_formula.replace("AA", str(value1)).replace(
                                "BB", str(value2)).replace("CC", str(value3)).replace("DD", str(value4))
                            msg = bytes.fromhex(formula)
                            result = witec_ser.write(msg)
                            data_span = str(witec_ser.readlines(1))

                            patch_payload_configuration = 'target_value=-1'
                            response = requests.request(
                                "PATCH", patch_url_configuration, headers=headers, data=patch_payload_configuration)
                        # start check to select parameters to calibration
                    # end is span calibration
                    # end calibration from CGA Mode
                # end read concentration
            # else:
                # print(json_get_configuration)
            time.sleep(2)
            witec_ser.close()  # Close serial port
        except serial.serialutil.SerialException as e:
            # set data now
            now = datetime.now()
            timestamp = now.strftime("%Y-%m-%d %H:%M:%S")

            # get configurations
            response_configuration = requests.request(
                "GET", get_url_configuration, headers=headers, data=get_payload)
            json_get_configuration = json.loads(response_configuration.text)

            # check configurations response
            if(json_get_configuration["success"] == True):
                # start read concentration
                response_sensor_lists = requests.request(
                    "GET", get_url_sensors, headers=headers, data=get_payload)
                json_get_sensor = json.loads(response_sensor_lists.text)
                for ch in json_get_sensor:
                    round_value = -1.111
                    patch_payload_sensor_values = 'value='+str(round_value)+''
                    response = requests.request(
                        "PATCH", patch_url_sensor_values + str(ch['id']), headers=headers, data=patch_payload_sensor_values)
            # else:
                # print(json_get_configuration)
            logf.write("Error "+timestamp+" : \n"+str(e))
            logf.close()
            time.sleep(5)
        time.sleep(2)
except Exception as e:
    now = datetime.now()
    timestamp = now.strftime("%Y-%m-%d %H:%M:%S")
    # print("[X]  Not connected ", e)
    logf.write("Error "+timestamp+" : \n".format(str(e)))
    logf.close()
