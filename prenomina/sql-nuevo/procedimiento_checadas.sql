USE [NUEVA_EMPRESA]
GO
/****** Object:  StoredProcedure [dbo].[reporte_checadas_excel_ctro]    Script Date: 20/02/2018 10:44:05 p. m. ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
ALTER proc [dbo].[reporte_checadas_excel_ctro]

@fecha1 AS VARCHAR(10), 
@fecha2 AS VARCHAR(10),
@CENTRO AS VARCHAR(12), 
@superv as VARCHAR(8),
@empresa as VARCHAR(8),
@tiponom as varchar(1),
@StringExtra as varchar(MAX),
@Tp as varchar(2),

@pagina AS VARCHAR(12),
@cantidad AS VARCHAR(10),
@busqueda AS VARCHAR(MAX),
@condicion AS VARCHAR(MAX),
@ordenar AS VARCHAR(MAX)

as

DECLARE @long as varchar(10)

if (SELECT charindex(' ', mascara) FROM empresas WHERE empresa = @empresa) = 0
	set @long = 0;
else 
	set @long = (SELECT LEN (LEFT (mascara, charindex(' ', mascara) -1)) AS mascara FROM empresas WHERE empresa = @empresa);

DECLARE @cols AS NVARCHAR(MAX), @query  AS NVARCHAR(MAX)

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

DECLARE @comSuperv VARCHAR(MAX)
if @superv = 0
	SET @comSuperv = '';
else 
	SET @comSuperv = 'L.supervisor = ' + @superv + ' AND';

SET @query = '
             SELECT                 
				codigo,
				Nombre,
				Sueldo,
				Tpo,
				'+@cols+',
				(TOTAL_REGISTROS * 2) AS TOTAL_REGISTROS,
				CEILING(TOTAL_REGISTROS / '+@cantidad+'.0) AS PAGINA
			 FROM
			 (
				SELECT 
					ROW_NUMBER() OVER (ORDER BY codigo) AS ROW_NUM,
					codigo, 
					Nombre,
					Sueldo, 
					''E'' Tpo, 
					'+@cols+',
					TOTAL_REGISTROS
				FROM 
				(		             
					SELECT E.codigo, 
						   E.ap_paterno+'' ''+E.ap_materno+'' ''+E.nombre AS Nombre,
						   E.sueldo, 
						   T.Actividad, 
						   CONVERT(VARCHAR(10), R.fecha, 103) fecha, 
						   R.checada,
						   (
							 SELECT 
								COUNT(E.codigo)
							 FROM Empleados AS E
							 INNER JOIN Llaves AS L ON L.codigo = E.codigo AND L.empresa = E.empresa
							 WHERE  E.Activo = ''S'' AND 
									'+@StringExtra+' AND
									'+@comSuperv+'
									E.empresa = '+@empresa+' AND L.tiponom = '+@tiponom+'
									'+@busqueda+'
						   ) AS TOTAL_REGISTROS
					FROM Empleados AS E  
					INNER JOIN Llaves AS L ON L.codigo = E.codigo AND L.Empresa = E.Empresa 
					LEFT JOIN relch_registro AS R ON R.codigo = E.Codigo AND R.checada<>''00:00:00'' AND (R.EoS = ''E'' OR R.EoS = ''1'')
					INNER JOIN Tabulador AS T ON T.Ocupacion = L.Ocupacion AND T.Empresa = L.Empresa    
			
					WHERE  E.Activo = ''S'' AND 
						   '+@StringExtra+' AND
						   '+@comSuperv+'
				           E.empresa = '+@empresa+' AND L.tiponom = '+@tiponom+'
				           '+@busqueda+'
				) x
				pivot 
				(
					min(checada)
					for fecha in ('+@cols +')
				) p 
				UNION ALL
				SELECT 
					ROW_NUMBER() OVER (ORDER BY codigo) AS ROW_NUM,
					codigo, 
					Actividad,
					Sueldo, 
					''S'' Tpo, 
					'+@cols+',
					TOTAL_REGISTROS
				FROM 
				(		             
					SELECT E.codigo, 
						   E.ap_paterno+'' ''+E.ap_materno+'' ''+E.nombre AS Nombre,
						   E.sueldo, 
						   T.Actividad, 
						   CONVERT(VARCHAR(10), R.fecha, 103) fecha, 
						   R.checada,
						   (
							 SELECT 
								COUNT(E.codigo)
							 FROM Empleados AS E
							 INNER JOIN Llaves AS L ON L.codigo = E.codigo AND L.empresa = E.empresa
							 WHERE  E.Activo = ''S'' AND 
									'+@StringExtra+' AND
									'+@comSuperv+'
									E.empresa = '+@empresa+' AND L.tiponom = '+@tiponom+'
									'+@busqueda+'
						   ) AS TOTAL_REGISTROS
					FROM Empleados AS E  
					INNER JOIN Llaves AS L ON L.codigo = E.codigo AND L.Empresa = E.Empresa 
					LEFT JOIN relch_registro AS R ON R.codigo = E.Codigo AND R.checada<>''00:00:00'' AND (R.EoS = ''S'' OR R.EoS = ''2'')
					INNER JOIN Tabulador AS T ON T.Ocupacion = L.Ocupacion AND T.Empresa = L.Empresa    
			
					WHERE  E.Activo = ''S'' AND 
						   '+@StringExtra+' AND
						   '+@comSuperv+'
				           E.empresa = '+@empresa+' AND L.tiponom = '+@tiponom+'
				           '+@busqueda+'
				) x				
				pivot 
				(
					max(checada)
					for fecha in ('+@cols +')                
				) q 
				
				) AS X 
				'+@condicion+'
				ORDER BY Codigo, Tpo'
            
execute(@query)