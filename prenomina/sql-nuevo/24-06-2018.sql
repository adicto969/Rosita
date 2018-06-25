SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO

---si marca error cambiar alter por create

alter proc [dbo].[proc_retardos]

@fecha1 AS VARCHAR(10), 
@fecha2 AS VARCHAR(10), 
@CENTRO AS VARCHAR(MAX), 
@superv AS VARCHAR(MAX), 
@empresa AS VARCHAR(8), 
@tiponom AS VARCHAR(1),
@EoS AS VARCHAR(1),
@minOmax AS VARCHAR(4), /*min cuando es E, max cuando es S*/
@Tp AS VARCHAR(2)

AS

DECLARE @long as varchar(10)
set @long = (SELECT LEN (LEFT (mascara, charindex(' ', mascara) -1)) AS mascara FROM empresas WHERE empresa = @empresa)

DECLARE @cols AS NVARCHAR(MAX), 

@query AS NVARCHAR(MAX) 

DECLARE @date DateTime

SET @cols = ''
SET @date = @fecha1

WHILE @date <= @fecha2
BEGIN
    if @date = @fecha2
		SET @cols = @cols + '[' + CONVERT(VARCHAR(10), @date, 103)
		+ '] ';
	else 
		SET @cols = @cols + '[' + CONVERT(VARCHAR(10), @date, 103)
		+ '],';		
    SET @date = @date + 1
END



set @query = CASE WHEN @Tp = '1'
THEN 
 'SELECT codigo, Nombre, sueldo, '''+ @EoS +''' Tpo, ' + @cols + ' 
			from ( SELECT empleados.codigo, ap_paterno+'' ''+ap_materno+'' ''+nombre AS Nombre,
			empleados.sueldo, Tabulador.Actividad, 
			convert(VARCHAR(10), relch_registro.fecha, 103) fecha, 
			relch_registro.tiempo 
			FROM Empleados 
			INNER JOIN Llaves ON Llaves.Codigo = Empleados.Codigo AND Llaves.Empresa = Empleados.Empresa
			INNER JOIN relch_registro on relch_registro.codigo = Empleados.Codigo 
			INNER JOIN Tabulador ON Tabulador.Ocupacion = Llaves.Ocupacion AND Tabulador.Empresa = Llaves.Empresa 
			WHERE Empleados.Activo = ''S'' and 
			'+@CENTRO+'
			and empleados.empresa = '+@empresa+' 
			and relch_registro.tiponom = '+@tiponom+' 
			and relch_registro.fecha BETWEEN ' + QUOTENAME(@fecha1,'''') + ' AND ' + QUOTENAME(@fecha2,'''') + ' 
			and relch_registro.checada<>''00:00:00'' and num_conc = 120 
			'+@superv+'
			) x pivot 
				( '+@MinOMax+'(tiempo) for fecha in (' + @cols + ') 
			) p 
			ORDER BY Codigo, Tpo' 
ELSE 

 'SELECT codigo, Nombre, sueldo, '''+ @EoS +''' Tpo, ' + @cols + ' 
			from ( SELECT empleados.codigo, ap_paterno+'' ''+ap_materno+'' ''+nombre AS Nombre,
			empleados.sueldo, Tabulador.Actividad, 
			convert(VARCHAR(10), relch_registro.fecha, 103) fecha, 
			relch_registro.tiempo 
			FROM Empleados 
			INNER JOIN Llaves ON Llaves.Codigo = Empleados.Codigo AND Llaves.Empresa = Empleados.Empresa
			INNER JOIN relch_registro on relch_registro.codigo = Empleados.Codigo 
			INNER JOIN Tabulador ON Tabulador.Ocupacion = Llaves.Ocupacion AND Tabulador.Empresa = Llaves.Empresa 
			WHERE Empleados.Activo = ''S'' and 
			'+@CENTRO+'
			and empleados.empresa = '+@empresa+' 
			and relch_registro.tiponom = '+@tiponom+' 
			and relch_registro.fecha BETWEEN ' + QUOTENAME(@fecha1,'''') + ' AND ' + QUOTENAME(@fecha2,'''') + ' 
			and relch_registro.checada<>''00:00:00'' and num_conc = 120 
			'+@superv+'
			) x pivot 
				( '+@MinOMax+'(tiempo) for fecha in (' + @cols + ') 
			) p 
			ORDER BY Codigo, Tpo' 

END
	execute(@query) 