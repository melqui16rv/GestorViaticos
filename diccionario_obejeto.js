// Diccionario de objetos para clasificación de CDP
// La clave es la palabra clave de clasificación
// El valor es un array con los textos completos asociados

const diccionarioObjeto = {
  "INSTRUCTOR": [
    "INSTRUCTOR. Contratar servicios profesionales y de apoyo a la gestión como instructor para el programa CampeSENA del centro industrial y de desarrollo empresarial de Soacha",
    "INSTRUCTOR. Contratar servicios profesionales y de apoyo a la gestión como instructor para el área de Desplazados del centro industrial y de desarrollo empresarial de Soacha",
    "INSTRUCTOR. Contratar servicios profesionales y de apoyo a la gestión como instructor para el programa de articulación con la media del centro industrial y de desarrollo empresarial de Soacha.",
    "INSTRUCTOR. Contratar servicios profesionales y de apoyo a la gestión como instructor para el área de FIC-Fondo de la industria de la construcción del centro industrial y de desarrollo empresarial de Soacha.",
    "INSTRUCTOR. Contratar servicios profesionales y de apoyo a la gestión como instructor en las modalidades virtual y presencial de formación titulada y complementaria del centro industrial y de desarrollo empresarial de Soacha.",
    "INSTRUCTOR. Contratar servicios profesionales y de apoyo a la gestión como instructor en formación titulada y complementaria ECONOMIA POPULAR del centro industrial y de desarrollo empresarial de Soacha."
  ],
  "MATERIALES FORMACION": [
    "MATERIALES FORMACION. Compra materiales para la formación programa CampeSENA",
    "MATERIALES FORMACION. Contratara monto agotable el suministro de materiales de formación para los programas de poblaciones vulnerables del Centro Industrial y Desarrollo Empresarial de Soacha",
    "MATERIALES FORMACION. Compra de materiales para la formación programa articulación con la educación media.",
    "MATERIALES FORMACION. Compra materiales para la formación programa Placa Huella FIC",
    "MATERIALES FORMACION. Compra materiales para la formación programas FIC",
    "MATERIALES FORMACION. Contratar a monto agotable el suministro de materiales de formación y elementos de programas y estrategias misionales del Centro Industrial y de Desarrollo Empresarial de Soacha",
    "MATERIALES FORMACION. Contratar a monto agotable el suministro de materiales de formación y elementos de programas de Economía Popular del Centro Industrial y de Desarrollo Empresarial de Soacha",
    "Contratar mediante el mecanismo de monto agotable la compra de materiales e insumos para los programas de formación ofertados y aquellos destinados al proceso de Producción de Centro para el mantenimiento de equipos de cómputo en el Centro Industrial",
    "Contratar mediante el mecanismo de monto agotable la compra de materiales e insumos para los programas de formación en las líneas de producción de alimentos y gestión agroindustrial en el Centro Industrial y de Desarrollo Empresarial de Soacha."
  ],
  "VIATICOS": [
    "VIATICOS. Desplazamiento para impartir formación del programa campeSENA",
    "VIATICOS. Gastos de transporte de los apoyos para el encuentro de seguridad y salud en el trabajo",
    "VIATICOS. Viáticos y gastos de viaje de instructores y equipo regional de Víctimas",
    "VIATICOS. Viáticos para atender las acciones de formación de articulación con la media",
    "VIATICOS. Viáticos de funcionarios, gastos de viaje de contratistas y gastos de transporte (aeropuertos - intermunicipal)",
    "VIATICOS. Viáticos de instructores para atender las acciones de formación de acuerdo con el comportamiento presupuestal del centro de formación.",
    "VIATICOS. DFP - Viáticos y gastos de viaje para el cumplimiento de la formación titulada y complementaria de ECONOMIA POPULAR considerando los cupos totales.",
    "VIATICOS. Viáticos y gastos de viaje para Dinamizadores de oferta CampeSENA y Full Popular",
    "VIATICOS. viáticos y gastos de viaje al interior área administrativa, según plan de acción radicado y aprobado por Comité Estratégico de Competitividad",
    "VIATICOS. viáticos y gastos de viaje participantes proyectos, alumnos (aprendices), según plan de acción radicado y aprobado por Comité Estratégico de Competitividad"
  ],
  "DOTACION": [
    "DOTACION. Contratar a monto agotable la compra de elementos de protección personal para funcionarios, trabajador oficial y brigada de emergencias del Centro Industrial y de Desarrollo Empresarial de Soacha"
  ],
  "SERVICIOS PERSONALES": [
    "SERVICIOS PERSONALES. Contratar la prestación de servicios profesionales carácter temporal para dirigir, controlar y evaluar las acciones de formación profesional integral que se ejecuten mediante alianzas y convenios con instituciones educativas del",
    "SERVICIOS PERSONALES. Contratar servicios profesionales y de apoyo a la gestión del área administrativa para el Centro Industrial y de Desarrollo Empresarial de Soacha.",
    "SERVICIOS PERSONALES. Contratar la prestación de servicios profesionales y de apoyo a la gestión para el área de Tecnologías de la Información y las Comunicaciones (TIC) del centro industrial y de desarrollo empresarial de Soacha.",
    "SERVICIOS PERSONALES. Contratar la prestación de servicios profesionales /o de apoyo a la gestión de carácter temporal, para contribuir con el cumplimiento de las metas establecidas para el proceso de Evaluación y Certificación de Competencias Labora",
    "SERVICIOS PERSONALES. Contratar la prestación de servicios profesionales y de apoyo a la gestión para la implementación del plan de bienestar del centro industrial y de desarrollo empresarial de Soacha.",
    "SERVICIOS PERSONALES. Prestar los servicios de apoyo a la gestión como conductor del Aula Móvil, atendiendo los desplazamientos programados por la subdirección Centro Industrial y de Desarrollo Empresarial de Soacha y sus Coordinaciones.",
    "SERVICIOS PERSONALES. Contratar la prestación de servicios profesionales y de apoyo a la gestión para el programa de Economía Campesina",
    "SERVICIOS PERSONALES. Contratar servicios profesionales y de apoyo a la gestión del área de infraestructura para el Centro Industrial y de Desarrollo Empresarial de Soacha.",
    "SERVICIOS PERSONALES. Contratar la prestación de servicios profesionales y de apoyo a la gestión para la ejecución del proyecto formulado y aprobado de Tecnoparque en el centro industrial y de desarrollo empresarial de Soacha.",
    "SERVICIOS PERSONALES. Contratar la prestación de servicios profesionales y de apoyo a la gestión para la ejecución del proyecto formulado y aprobado de Tecnoacademia en el centro industrial y de desarrollo empresarial de Soacha.",
    "SERVICIOS PERSONALES. Contratar la prestación de servicios profesionales y de apoyo a la gestión para los procesos relacionados con la gestión curricular para la (diseño, desarrollo y gestión de permisos) priorizando el programa de formación tecnólog",
    "SERVICIOS PERSONALES. Contratar la prestación de servicios profesionales y de apoyo a la gestión para la ejecución de investigación aplicada y semilleros de investigación en Centros de Formación"
  ],
  "MONITORES": [
    "MONITORES. Monitorias como estímulo a aprendices destacados según normativa de monitorias vigente."
  ],
  "SERVICIOS PUBLICOS": [
    "SERVICIOS PUBLICOS. Pago de servicios públicos."
  ],
  "IMPUESTOS": [
    "IMPUESTOS. Pago impuesto predial, impuesto de vehículo, otros impuestos."
  ],
  "PAPELERIA": [
    "PAPELERIA. Contratar a monto agotable la compra de elementos de oficina y consumibles de impresión para el Centro Industrial y de Desarrollo Empresarial de Soacha",
    "PAPELERIA. Contratar mediante el mecanismo de monto agotable la compra de elementos de oficina y consumibles de impresión para el Centro Industrial y de Desarrollo Empresarial de Soacha."
  ],
  "OTROS MATERIALES Y SUMINISTROS": [
    "OTROS MATERIALES Y SUMINISTROS. Contratar la Compra de tarjeta recargable canjeable por combustible (gasolina y diésel) en estaciones de servicio a nivel nacional en las denominaciones requeridas por la entidad para el aprovisionamiento de combustibl",
    "OTROS MATERIALES Y SUMINISTROS. Contratar la adquisición de materiales, insumos y servicios (con suministro de combustible, lubricantes, llantas, baterías, filtros, así como la cobertura de gastos asociados a parqueadero, lavado de vehículos, pago de"
  ],
  "PROTECCION APRENDICES": [
    "PROTECCION APRENDICES. Contratar a monto agotable la compra de elementos de protección personal para los aprendices del Centro Industrial y de Desarrollo Empresarial de Soacha",
    "PROTECCION APRENDICES. Contratar a monto agotable la compra de elementos de protección para los aprendices del Centro Industrial y de Desarrollo Empresarial de Soacha.",
    "Contratar mediante el mecanismo de monto agotable la compra de elementos de protección personal para los aprendices de programas FIC, beneficiarios de apoyos de sostenimiento regular, funcionarios, trabajador oficial y la brigada de emergencias del"
  ],
  "NOMINA": [
    "NOMINA. Contratar la compra de tarjeta de consumo para la alimentación del trabajador oficial del Centro Industrial y de Desarrollo Empresarial de Soacha"
  ],
  "BIENESTAR APRENDICES": [
    "BIENESTAR APRENDICES. Vacunas aprendices.",
    "BIENESTAR APRENDICES. Gastos bienestar alumnos para la ejecución de las actividades del PNIBA de programas de FIC",
    "BIENESTAR APRENDICES. Gastos bienestar alumnos para la ejecución de las actividades del PNIBA del Centro Industrial y de Desarrollo Empresarial de Soacha",
    "BIENESTAR APRENDICES. Contratar mediante el mecanismo de monto agotable el suministro de alimentos e hidratación para la implementación del Plan Nacional Integral de Bienestar del Aprendiz en el Centro Industrial y de Desarrollo Empresarial de Soacha",
    "BIENESTAR APRENDICES. Contratar mediante el mecanismo de monto agotable la adquisición de prendas deportivas y elementos complementarios para la participación de los aprendices en actividades físicas, recreativas y culturales propias de la implement",
    "BIENESTAR APRENDICES. Contratar mediante el mecanismo de monto agotable los servicios de transporte y realización de convivencias y salidas de fortalecimiento de programas FIC en el marco de la implementación del Plan Nacional Integral de Bienestar",
    "BIENESTAR APRENDICES. Pago de Apoyo Temporal transporte del programa de Bienestar al Aprendiz del Centro Industrial y de Desarrollo Empresarial",
    "BIENESTAR APRENDICES. Pago de Apoyos de Alimentación del programa de Bienestar al Aprendiz del Centro Industrial y de Desarrollo Empresarial",
    "BIENESTAR APRENDICES. Pago de Apoyos de Medios Tecnológicos del programa de Bienestar al Aprendiz del Centro Industrial y de Desarrollo Empresarial",
    "BIENESTAR APRENDICES. Resoluciones de viáticos de los aprendices para el cumplimiento de lo dispuesto en la Resolución Nro. 1-01399 de 2021 Por la cual se adopta el Plan Nacional Integral de Bienestar de los aprendices"
  ],
  "APOYO APRENDICES": [
    "APOYO APRENDICES. Atender apoyos de sostenimiento FIC regular para los aprendices que cumplen con los requisitos de acuerdo con la normatividad vigente",
    "APOYO APRENDICES. Atender apoyos de sostenimiento regular para los aprendices que cumplen con los requisitos de acuerdo con la normatividad vigente"
  ],
  "MANTENIMIENTO BIENES": [
    "MANTENIMIENTO BIENES. Contratar el servicio de mantenimiento preventivo y correctivo a todo costo (con suministros, repuestos y mano de obra) para el parque automotor del Centro Industrial y de Desarrollo Empresarial de Soacha.",
    "MANTENIMIENTO BIENES. Contratar el servicio de mantenimiento preventivo y correctivo a todo costo (con suministros, repuestos y mano de obra), incluyendo la gestión de revisión técnico-mecánica, infraestructura y adecuación, la actualización o cambio",
    "MANTENIMIENTO BIENES. Mantenimiento de bienes muebles, enseres, maquinaria, equipo, transportes y software para atención de proyectos de Producción de Centros",
    ": MANTENIMIENTO BIENES. Contratar los servicios integrales de mantenimiento preventivo y correctivo a todo costo que incluyan suministros repuestos y mano de obra, para el parque automotor y el Aula Móvil del Centro Industrial y de Desarrollo Empres"
  ],
  "MOBILIARIO": [
    "MOBILIARIO. Compra de mobiliario que incluye mesas, sillas, armarios, bancos de trabajo, archivadores, escritorios, tableros, entre otros"
  ],
  "OTROS EQUIPOS": [
    "OTROS EQUIPOS. Compra de básculas para el pesaje de residuos",
    "OTROS EQUIPOS. Asignación de recursos para dotación de ambientes convencionales de formación, áreas administrativas y de bienestar de la subsede de Guachetá-Cundinamarca",
    "OTROS EQUIPOS. Contratar la compra de mobiliario, equipos de funcionamiento, herramientas y demás elementos de dotación para la sede \"Ciudad Verde\" del Centro Industrial y de Desarrollo Empresarial de Soacha, incluyendo suministro, transporte, inst"
  ],
  "ECCL SERVICIOS PERSONALES": [
    "ECCL SERVICIOS PERSONALES. Contratar la prestación de servicios de evaluadores que ejecutan el proceso de gestión de evaluación y certificación de competencias laborales en el Centro Industrial y de Desarrollo Empresarial de Soacha"
  ],
  "VIATICOS FORMACION": [
    "VIATICOS FORMACION. Viáticos para conductor aula móvil."
  ],
  "MATERIALES FORMACIÓN": [
    "MATERIALES FORMACIÓN. Materiales e insumo para atención de proyectos de Producción de Centros."
  ],
  "ADECUACIONES": [
    "ADECUACIONES. REALIZAR ADECUACIONES A TODO COSTO, INCLUYENDO SUMINISTRO DE MATERIALES, MANO DE OBRA Y SERVICIOS TÉCNICOS, PARA LAS ADECUACIONES DE ESPACIOS EN LA INFRAESTRUCTURA DE LA SEDE “CIUDAD VERDE” DEL CENTRO INDUSTRIAL Y DE DESARROLLO EMPRESAR",
    "ADECUACIONES. Pago del permiso requerido para la instalación y mantenimiento de publicidad exterior visual en las instalaciones del Centro Industrial y de Desarrollo Empresarial de Soacha",
    "ADECUACIONES. Realizar adecuaciones integrales a todo costo para la infraestructura de la sede \"Ciudad Verde\" del Centro Industrial y de Desarrollo Empresarial de Soacha, incluyendo suministro de materiales, mano de obra y servicios técnicos especial",
    "ADECUACIONES. Realizar la interventoría técnica, administrativa, jurídica, financiera, contable, ambiental y social al contrato de obra que celebre el CIDE para la ejecución del objeto \"Realizar adecuaciones integrales a todo costo para la infraestr\""
  ],
  "MANTENIMIENTO INMUEBLES": [
    "MANTENIMIENTO INMUEBLES. Contratar el mantenimiento preventivo y correctivo de las motobombas y de la red hidráulica, red sanitaria, y de alcantarillado, redes hidráulicas y redes de acueducto, que incluya la limpieza, inspección, reparación de tuber",
    "MANTENIMIENTO INMUEBLES. CONTRATAR A MONTO AGOTABLE EL SUMINISTRO DE MATERIALES DE FERRETERÍA PARA EL CENTRO INDUSTRIAL Y DE DESARROLLO EMPRESARIAL DE SOACHA",
    "MANTENIMIENTO INMUEBLES. Contratar a todo costo el servicio de mantenimiento preventivo y correctivo de las plantas eléctricas y redes, asegurando el adecuado funcionamiento durante los cortes de energía, incluyendo pruebas de carga, limpieza de comp",
    "MANTENIMIENTO INMUEBLES. Contratar el servicio de mantenimiento preventivo y correctivo de las instalaciones solares fotovoltaicas, que incluye la limpieza de paneles solares, revisión de inversores, conexiones y baterías, asegurando el óptimo rendim",
    "MANTENIMIENTO INMUEBLES. CONTRATAR A MONTO AGOTABLE EL SERVICIO INTEGRAL DE SANEAMIENTO AMBIENTAL (FUMIGACIÓN, CONTROL DE ROEDORES, MANTENIMIENTO Y LAVADO DE TANQUE DE ALMACENAMIENTO DE AGUA POTABLE, PODA Y TALA SANITARIA DE ÁRBOLES) PARA LAS SEDES D",
    "MANTENIMIENTO INMUEBLES. Contratar a todo costo la impermeabilización y la construcción de cárcamos para el piso del cuarto de residuos",
    "MANTENIMIENTO INMUEBLES. Contratar a todo costo el mantenimiento y reparación de las cubiertas del Centro Industrial y de Desarrollo Empresarial de Soacha.",
    "MANTENIMIENTO INMUEBLES. Contratar el servicio de mantenimiento preventivo y correctivo de ascensores, incluyendo la revisión de componentes eléctricos y mecánicos, lubricación, ajustes, y reparación o reemplazo de piezas necesarias, con el fin de ga",
    "MANTENIMIENTO INMUEBLES. Contratación del servicio de mantenimiento preventivo y correctivo de los sistemas de extracción de humos, que incluye la limpieza de ductos, ventiladores y filtros, así como la inspección de motores y controladores, para gar",
    "MANTENIMIENTO INMUEBLES. Contratar a todo costo el servicio de mantenimiento preventivo y correctivo de las redes contra incendios, incluyendo la inspección y reparación de sistemas de tuberías, válvulas, rociadores y equipos relacionados, para garan",
    "MANTENIMIENTO INMUEBLES. Contratar el servicio de mantenimiento de cuarto de almacenamiento para las sedes del Centro Industrial y de Desarrollo Empresarial de Soacha",
    "MANTENIMIENTO INMUEBLES. Contratar el servicio integral para el cumplimiento del Plan de Emergencias del Centro Industrial y de Desarrollo Empresarial de Soacha",
    "MANTENIMIENTO INMUEBLES. Contratar el mantenimiento preventivo y correctivo de motobombas y de las redes hidráulicas, sanitarias, de alcantarillado y de acueducto, que incluya la limpieza, inspección y reparación de tuberías, válvulas y accesorios,",
    "MANTENIMIENTO INMUEBLES. Contratar a todo costo el mantenimiento preventivo y correctivo de las cubiertas de las sedes del Centro Industrial y de Desarrollo Empresarial de Soacha",
    "MANTENIMIENTO INMUEBLES. Contratar mediante el mecanismo de monto agotable el suministro de materiales de ferretería para el Centro Industrial y de Desarrollo Empresarial de Soacha",
    "MANTENIMIENTO INMUEBLES. Contratar a todo costo el servicio integral de mantenimiento preventivo y correctivo de plantas eléctricas y de redes, y ascensores del Centro Industrial y de Desarrollo Empresarial de Soacha, garantizando la operatividad,",
    "MANTENIMIENTO INMUEBLES. Contratar a todo costo el mantenimiento preventivo y correctivo de las instalaciones solares fotovoltaicas en el Centro Industrial y de Desarrollo Empresarial de Soacha, garantizando la limpieza y revisión de paneles, invers",
    "MANTENIMIENTO INMUEBLES. Contratar a todo costo el servicio integral para el cumplimiento del Plan de Emergencias y el mantenimiento preventivo y correctivo de las redes contra incendios en las sedes del Centro Industrial y de Desarrollo Empresarial",
    "MANTENIMIENTO INMUEBLES. Contratar mediante el mecanismo de monto agotable y a todo costo el servicio integral comprendido por el saneamiento ambiental (fumigación, control de roedores, mantenimiento, poda y tala sanitaria de árboles), la constr"
  ],
  "BIENESTAR EMPLEADOS": [
    "BIENESTAR EMPLEADOS. Servicios de bienestar social, actividades culturales recreativas y deportivas"
  ]
};

module.exports = diccionarioObjeto;
