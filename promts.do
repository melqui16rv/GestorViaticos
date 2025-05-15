analiza como se menejan las cookies de los filtros, esto para hacer lo mismo con diferentes archivos, por lo que debes entender como funsiona y como es que funsiona, para que lo apliquemos a otros archivos, solo ve y analiza, ya que es muy importante esta funsionalidad ya que he visto que las cookies se guardan de una forma impresionante y hace que los filtros se mantengan asi se retablesca la pagina
adjuntar: metodosGestor.php, index.php/presupuesto

ahora que entendiste como funsiona, quiero adaptar el mismo sistema de cookies para los filtros de la vista dashboard.php, Graficas.php y dashboard_content.php, pero 
ten cuidado ya que hay varios pasos para que funsione como en el modelo que analizate, para esto mira si hay que hacer cambios en los metodos ademas de validar el 
archivo cada archivo cuidadosamente, quiero que si tienes dudas me digas y yo te respondere, ademas no es necesario que resuelvas todo en una sola respuesta, podemos
dividir para que vayamos resolviendo paso a paso.

Donde aplicar las cookies?
1. quiero que las cookies se mantengan en la barra lateral izquierda de la vista dashboard.php esto para que se guarde que vista estaba viendo si la de Graficas o la de dashboard_content

2. en la vista Graficas.php que se mantengan las graficas de tortas agregadas y seleccionadas, ademas de la seleción de los label de cada trafica sea de barras o de tortas ya que al deseleccionar
se oculatara de la grafica lo mismo al seleccionar esto mostrarara la el dato en las graficas, esto es lo que quiero mantener.

3. en la vista dashboard_content.php quiero que se mantengan de igual manera los label y tambien la selección que se le hace al total para que muestre de forma disgregada la cantidad por dependencia.

vamos a resolver dime que necesitas..



si te fijas en el nav.php los roles 4,5 y 6 los lleva a "/viaticosapp/app/sennova/general/index.php" lo cual esta bien pero tenemos que modificar algunas cosas 
ya que no todos pueden ver toda esa información para el rol 4 esta bien ya que es el rol que engloba las demas areas pero pero para el rol 5 y 6 queiro que al rol 5 
solo le muestre la información correspondiente a la dependecia "69" que hace referencia a Tecnoparque entonces en todas las vistas que te proporcione y que estan 
enlazadas al index.php que son dirigidos en el nav pues deben ser acorde a la dependencia "62" y para el caso del rol 6 debe ser relacionado con la dependencia "70" 
que hace referencia a Tecnoacademia.

la idea es que no se requieran hacer mas archivos, puedes modificar los metodos y vistas todo lo que sea necesario
-----------

y para la parte de de los viaticos tambien se aplico que solo trajera los viaticos para las dependencias como hisimos en las otras vistas? es que de momento solo hay 
un viatico registrado y me aparece en los 3 roles lo cual esta mal ya que debe ser de alguno pero no de los 3...

En la tabla de viaticos aun me aprece el registro de la orden de pago del viatico lo cual esta mal ya que estoy desde el rol 6 y el registro hace referencia a el 5 
por lo que no es bueno que tenga información del otro

