from time import sleep
from pymodbus.client.sync import ModbusSerialClient
import minimalmodbus
import mysql.connector
import serial  # Import module


# rs485 = minimalmodbus.Instrument('COM5', 1, 'rtu', False)
# rs485.serial.baudrate = 9600
# rs485.serial.parity = serial.PARITY_NONE
# rs485.serial.bytesize = 8
# rs485.serial.stopbits = 1
# rs485.serial.timeout = 5

# reg = rs485.read_registers(1, 8, 3)
# print(reg)
# exit()


try:
    # portx = "/dev/ttyUSB0"
    portx = "COM5"
    bps = 9600
    # time-out,None: Always wait for the operation, 0 to return the request result immediately, and the other values are waiting time-out.(In seconds)
    timex = 1
    # print("Serial details parameters:", ser)

    while True:
        ser = serial.Serial(portx, bps, timeout=timex)
        # Hexadecimal transmission
        #msg = bytes.fromhex("33 03 9D A4 00 02 AE 56")
        # msg = bytes.fromhex("01 0F 00 00 00 08 01 00 FE 95")
        msg = bytes.fromhex("01 0F 00 00 00 08 01 01 3F 55")
        result = ser.write(msg)
        # msg3 = bytes.fromhex("01 0F 00 00 00 08 01 02 7F 54")
        # result3 = ser.write(msg3)
        # msg = bytes.fromhex("17 00 00 00 00 00 55 30")
        # result=ser.write(chr(0x06).encode("utf-8"))#Writing data
        # print("Write total bytes:", result)
        # exit()

        # Hexadecimal Reading
        # print(ser.read().hex())#Read a byte
        # print(ser.read(8).hex())#Read a byte
        print(ser.readlines(1))
        # print(result)
        # print(result3)
        # ser.close()  # Close serial port
        sleep(5)
        # msg2 = bytes.fromhex("01 0F 00 00 00 08 01 00 FE 95")
        msg2 = bytes.fromhex("01 0F 00 00 00 08 01 02 7F 54")
        result2 = ser.write(msg2)
        print(ser.readlines(1))
        print(result2)
        ser.close()  # Close serial port
        sleep(5)
        # msg = bytes.fromhex("17 00 00 00 00 00 55 30")
        # exit()

except Exception as e:
    print("---abnormal---: ", e)
