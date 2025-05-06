# 💼 Sistema de Control de Gastos y Generación de Pólizas

Este repositorio contiene un sistema para el **control de gastos empresariales**, con funcionalidades que permiten la **lectura de facturas XML**, la **creación de pólizas contables** agrupando gastos por objetivo, la **generación de archivos PDF** y la **visualización de reportes** con gráficas interactivas.

## ⚙️ Tecnologías Utilizadas

- **PHP**: Lógica del backend y manejo de archivos
- **MySQL/MariaDB**: Almacenamiento de facturas, empleados y pólizas
- **JavaScript + Chart.js**: Reportes gráficos por empleado y por objetivo
- **DOMPDF o TCPDF**: Generación de archivos PDF
- **XMLReader / SimpleXML**: Lectura de archivos de facturación en XML
- **Bootstrap 5**: Interfaz moderna y responsive

## 📌 Funcionalidades

- 📂 **Lectura de archivos XML**  
  Carga automática de facturas en formato XML para su procesamiento.

- 🧾 **Creación de pólizas contables**  
  Agrupación de gastos asociados a un mismo objetivo y generación de una póliza PDF.

- 👤 **Reporte de gastos por empleado**  
  Visualización interactiva de los gastos individuales con **Chart.js**.

- 🗃️ **Gestión de archivos**  
  Almacenamiento estructurado de facturas, pólizas y registros por fecha, empleado y objetivo.

## 📁 Estructura del Proyecto (Ejemplo)

