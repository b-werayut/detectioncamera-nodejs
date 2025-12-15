/*
  Warnings:

  - Made the column `foldername` on table `TmstCameraDetectionLogs` required. This step will fail if there are existing NULL values in that column.
  - Made the column `point` on table `TmstPowerDetectionLogs` required. This step will fail if there are existing NULL values in that column.
  - Made the column `status` on table `TmstPowerDetectionLogs` required. This step will fail if there are existing NULL values in that column.
  - Made the column `value` on table `TmstPowerDetectionLogs` required. This step will fail if there are existing NULL values in that column.
  - Made the column `timestamp` on table `TmstPowerDetectionLogs` required. This step will fail if there are existing NULL values in that column.

*/
BEGIN TRY

BEGIN TRAN;

-- AlterTable
ALTER TABLE [dbo].[TmstCameraDetectionLogs] DROP CONSTRAINT [TmstCameraDetectionLogs_linesendstatus_df],
[TmstCameraDetectionLogs_picstatus_df],
[TmstCameraDetectionLogs_vdostatus_df];
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [foldername] NVARCHAR(1000) NOT NULL;
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [linesendstatus] NVARCHAR(1000) NOT NULL;
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [picstatus] NVARCHAR(1000) NOT NULL;
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [vdostatus] NVARCHAR(1000) NOT NULL;

-- AlterTable
ALTER TABLE [dbo].[TmstPowerDetectionLogs] ALTER COLUMN [point] NVARCHAR(1000) NOT NULL;
ALTER TABLE [dbo].[TmstPowerDetectionLogs] ALTER COLUMN [status] NVARCHAR(1000) NOT NULL;
ALTER TABLE [dbo].[TmstPowerDetectionLogs] ALTER COLUMN [value] INT NOT NULL;
ALTER TABLE [dbo].[TmstPowerDetectionLogs] ALTER COLUMN [timestamp] DATETIME2 NOT NULL;

COMMIT TRAN;

END TRY
BEGIN CATCH

IF @@TRANCOUNT > 0
BEGIN
    ROLLBACK TRAN;
END;
THROW

END CATCH
