BEGIN TRY

BEGIN TRAN;

-- AlterTable
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [linesendstatus] NVARCHAR(1000) NULL;
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [picstatus] INT NULL;
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ALTER COLUMN [vdostatus] INT NULL;

COMMIT TRAN;

END TRY
BEGIN CATCH

IF @@TRANCOUNT > 0
BEGIN
    ROLLBACK TRAN;
END;
THROW

END CATCH
