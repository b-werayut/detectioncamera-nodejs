BEGIN TRY

BEGIN TRAN;

-- AlterTable
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ADD CONSTRAINT [TmstCameraDetectionLogs_picstatus_df] DEFAULT 0 FOR [picstatus], CONSTRAINT [TmstCameraDetectionLogs_vdostatus_df] DEFAULT 0 FOR [vdostatus];

COMMIT TRAN;

END TRY
BEGIN CATCH

IF @@TRANCOUNT > 0
BEGIN
    ROLLBACK TRAN;
END;
THROW

END CATCH
