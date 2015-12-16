#!/usr/bin/python
# -*- coding: utf-8 -*-

import os
import glob
import time
import MySQLdb as mdb

os.system('modprobe w1-gpio')
os.system('modprobe w1-therm')

base_dir = '/sys/bus/w1/devices/'
device_folder = glob.glob(base_dir + '28*')[0]
device_file = device_folder + '/w1_slave'

def read_temp_raw():
    f = open(device_file, 'r')
    lines = f.readlines()
    f.close()
    return lines

def read_temp():
    lines = read_temp_raw()
    while lines[0].strip()[-3:] != 'YES':
        time.sleep(0.2)
        lines = read_temp_raw()
    equals_pos = lines[1].find('t=')
    if equals_pos != -1:
        temp_string = lines[1][equals_pos+2:]
        temp_c = float(temp_string) / 1000.0
        return temp_c

while True:

    try:
        pi_temp = read_temp()
        con = mdb.connect('localhost', \
                          'pi_insert', \
                          'insert01', \
                          'measurements');
        cur = con.cursor()
        cur.execute("""INSERT INTO temperature(temperature) \
                       VALUES(%s)""", (pi_temp))
        con.commit()

    except mdb.Error, e:
        con.rollback()
        print "Error %d: %s" % (e.args[0],e.args[1])
        sys.exit(1)

    finally:
        if con:
            con.close()

	time.sleep(10)