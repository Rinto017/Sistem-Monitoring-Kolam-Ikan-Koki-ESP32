# Sistem Monitoring dan Filtrasi Otomatis Kolam Ikan Koki Berbasis ESP32

Project ini merupakan sistem monitoring dan filtrasi otomatis untuk kolam ikan koki menggunakan ESP32. Sistem membaca suhu air menggunakan sensor DS18B20 dan membaca tingkat kekeruhan air menggunakan sensor turbidity. Apabila air terdeteksi keruh, maka relay akan mengaktifkan pompa untuk membantu proses filtrasi air.

## Komponen yang Digunakan

- ESP32 DOIT DevKit V1
- Sensor suhu DS18B20
- Sensor turbidity
- Relay module
- Pompa air
- Kabel jumper
- Power supply

## Pin yang Digunakan

| Komponen | Pin ESP32 |
|---|---|
| Relay | GPIO 27 |
| Sensor Turbidity | GPIO 34 |
| Sensor Suhu DS18B20 | GPIO 4 |

## Library yang Dibutuhkan

Sebelum program di-upload ke ESP32 melalui Arduino IDE, install library berikut:

- OneWire
- DallasTemperature

Cara install library di Arduino IDE:

1. Buka Arduino IDE
2. Pilih menu Sketch
3. Pilih Include Library
4. Pilih Manage Libraries
5. Cari dan install OneWire
6. Cari dan install DallasTemperature

## Cara Kerja Sistem

1. ESP32 membaca suhu air dari sensor DS18B20.
2. ESP32 membaca nilai kekeruhan air dari sensor turbidity.
3. Data suhu dan turbidity ditampilkan pada Serial Monitor.
4. Jika air terdeteksi keruh, relay akan menyalakan pompa.
5. Jika air sudah kembali jernih, relay akan mematikan pompa.

## Kalibrasi Sensor Turbidity

Berdasarkan hasil pengujian:

- Air bersih: sekitar 4095
- Air kotor: sekitar 3324 - 3530

Batas yang digunakan pada program:

- Pompa ON jika nilai turbidity <= 3600
- Pompa OFF jika nilai turbidity >= 3800

## Catatan

Jika file dibuka melalui github.dev atau VS Code Web, mungkin muncul error:

cannot open source file "OneWire.h"  
cannot open source file "DallasTemperature.h"

Error tersebut terjadi karena library Arduino tidak tersedia di lingkungan GitHub Web. Program tetap dapat dijalankan melalui Arduino IDE setelah library yang dibutuhkan diinstall.
