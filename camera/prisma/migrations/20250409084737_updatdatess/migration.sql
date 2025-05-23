BEGIN TRY

BEGIN TRAN;

-- AlterTable
ALTER TABLE [dbo].[TmstCameraDetectionLogs] ADD CONSTRAINT [TmstCameraDetectionLogs_updatedAt_df] DEFAULT 'No update Log' FOR [updatedAt];

COMMIT TRAN;

END TRY
BEGIN CATCH

IF @@TRANCOUNT > 0
BEGIN
    ROLLBACK TRAN;
END;
THROW

END CATCH
