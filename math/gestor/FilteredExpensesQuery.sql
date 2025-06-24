SELECT Objeto_del_Compromiso, Valor_Neto
FROM op
WHERE (UPPER(Objeto_del_Compromiso) LIKE '%VIATICOS%'
   OR UPPER(Objeto_del_Compromiso) LIKE '%VIATI%'
   OR UPPER(Objeto_del_Compromiso) LIKE '%TRANSPO%')
   AND UPPER(Estado) LIKE '%PAGADA%';